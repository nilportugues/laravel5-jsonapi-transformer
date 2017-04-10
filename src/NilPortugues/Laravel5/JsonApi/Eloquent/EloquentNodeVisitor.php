<?php
namespace NilPortugues\Laravel5\JsonApi\Eloquent;

use Xiag\Rql\Parser\Query;
use Illuminate\Database\Eloquent\Builder;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;
use Xiag\Rql\Parser\Glob;

class EloquentNodeVisitor
{
	public function visit(Query $query, Builder $builder)
	{		
		if ($query->getQuery() !== null) {
			$this->visitQueryNode($query->getQuery(), $builder);
		}
	}

	private function visitQueryNode(AbstractQueryNode $node, Builder $builder)
	{
		if ($node instanceof AbstractScalarOperatorNode) {
			$this->visitScalarNode($node, $builder);
		} elseif ($node instanceof AbstractArrayOperatorNode) {
			$this->visitArrayNode($node, $builder);
		} elseif ($node instanceof AbstractLogicalOperatorNode) {
			$this->visitLogicalNode($node, $builder);
		} else {
			throw new \LogicException(sprintf('Unknown node "%s"', $node->getNodeName()));
		}
	}
	
	private function visitScalarNode(AbstractScalarOperatorNode $node, Builder $builder)
	{
		static $operators = [
			'like'  => 'LIKE',
			'eq'    => '=',
			'ne'    => '<>',
			'lt'    => '<',
			'gt'    => '>',
			'le'    => '<=',
			'ge'    => '>=',
		];
		
		if (!isset($operators[$node->getNodeName()])) {
			throw new \LogicException(sprintf('Unknown scalar node "%s"', $node->getNodeName()));
		}
		
		$value = $node->getValue();
		
		if ($value instanceof Glob) {
			$value = $value->toLike();
		} elseif ($value instanceof \DateTimeInterface) {
			$value = $value->format(DATE_ISO8601);
		}
		
		$builder->where(
			$node->getField(),
			$operators[$node->getNodeName()],
			$value
		);
	}
	
	private function visitArrayNode(AbstractArrayOperatorNode $node, Builder $builder)
	{
		switch($node->getNodeName()){
			case 'in':
				$builder->whereIn(
					$node->getField(),
					$value
				);
			case 'out':
				$builder->whereNotIn(
					$node->getField(),
					$value
				);
			default:
				throw new \LogicException(sprintf('Unknown array node "%s"', $node->getNodeName()));
		}
	}
	
	private function visitLogicalNode(AbstractLogicalOperatorNode $node, Builder $builder)
	{
		if ($node->getNodeName() !== 'and' && $node->getNodeName() !== 'or') {
			throw new \LogicException(sprintf('Unknown or unsupported logical node "%s"', $node->getNodeName()));
		}
				
		$builder->where(function ($constraintGroupBuilder) {
			foreach ($node->getQueries() as $query) {
				$this->visitQueryNode($query, $constraintGroupBuilder);
			}		
		}, null, null, $node->getNodeName());		
	}
}