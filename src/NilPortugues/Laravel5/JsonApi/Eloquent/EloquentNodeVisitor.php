<?php

namespace NilPortugues\Laravel5\JsonApi\Eloquent;

use Illuminate\Database\Query\Builder;
use Xiag\Rql\Parser\Glob;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Query;

/**
 * RQL node visitor for constructing Eloquent queries.
 *
 * @author srottem
 */
class EloquentNodeVisitor
{
    /**
     * Populates the provided builder from the provided RQL query instance.
     *
     * @param Query   $query   The RQL query to populate the Eloquent builder from
     * @param Builder $builder The Eloquent query builder to populate
     */
    public function visit(Query $query, Builder $builder)
    {
        if ($query->getQuery() !== null) {
            $this->visitQueryNode($query->getQuery(), $builder);
        }
    }

    /**
     * Processes a query node.
     *
     * @param AbstractQueryNode $node    The node to process
     * @param Builder           $builder The Eloquent builder to populate
     * @param string            $boolean The operator to use when appending where clauses
     *
     * @throws \LogicException Thrown if the node is of an unknown type
     */
    private function visitQueryNode(AbstractQueryNode $node, Builder $builder, $boolean = 'and')
    {
        if ($node instanceof AbstractScalarOperatorNode) {
            $this->visitScalarNode($node, $builder, $boolean);
        } elseif ($node instanceof AbstractArrayOperatorNode) {
            $this->visitArrayNode($node, $builder, $boolean);
        } elseif ($node instanceof AbstractLogicalOperatorNode) {
            $this->visitLogicalNode($node, $builder, $boolean);
        } else {
            throw new \LogicException(sprintf('Unknown node "%s"', $node->getNodeName()));
        }
    }

    /**
     * Processes a scalar node.
     *
     * @param AbstractScalarOperatorNode $node    The node to process
     * @param Builder                    $builder The Eloquent builder to populate
     * @param unknown                    $boolean The operator to use when appending where clauses
     *
     * @throws \LogicException Thrown if the node cannot be processed
     */
    private function visitScalarNode(AbstractScalarOperatorNode $node, Builder $builder, $boolean)
    {
        static $operators = [
            'like' => 'LIKE',
            'eq' => '=',
            'ne' => '<>',
            'lt' => '<',
            'gt' => '>',
            'le' => '<=',
            'ge' => '>=',
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

        if ($value === null) {
            if ($node->getNodeName() === 'eq') {
                $builder->whereNull($node->getField(), $boolean);
            } elseif ($node->getNodeName() === 'ne') {
                $builder->whereNotNull($node->getField(), $boolean);
            } else {
                throw new \LogicException(sprintf("Only the 'eq' an 'ne' operators can be used when comparing to 'null()'."));
            }
        } else {
            $builder->where(
                $node->getField(),
                $operators[$node->getNodeName()],
                $value,
                $boolean
            );
        }
    }

    /**
     * Processes an array node.
     *
     * @param AbstractArrayOperatorNode $node    The node to process
     * @param Builder                   $builder The Eloquent builder to populate
     * @param unknown                   $boolean The operator to use when appending where clauses
     *
     * @throws \LogicException Thrown if the node cannot be processed
     */
    private function visitArrayNode(AbstractArrayOperatorNode $node, Builder $builder, $boolean)
    {
        static $operators = [
            'in',
            'out',
        ];

        if (!in_array($node->getNodeName(), $operators)) {
            throw new \LogicException(sprintf('Unknown array node "%s"', $node->getNodeName()));
        }

        $negate = false;

        if ($node->getNodeName() === 'out') {
            $negate = true;
        }

        $builder->whereIn(
            $node->getField(),
            $node->getValues(),
            $boolean,
            $negate
        );
    }

    /**
     * Processes a logical node.
     *
     * @param AbstractLogicalOperatorNode $node    The node to process
     * @param Builder                     $builder The Eloquent builder to populate
     * @param unknown                     $boolean The operator to use when appending where clauses
     *
     * @throws \LogicException Thrown if the node cannot be processed
     */
    private function visitLogicalNode(AbstractLogicalOperatorNode $node, Builder $builder, $boolean)
    {
        if ($node->getNodeName() === 'and' || $node->getNodeName() === 'or') {
            $builder->where(\Closure::bind(function ($constraintGroupBuilder) use ($node) {
                foreach ($node->getQueries() as $query) {
                    $this->visitQueryNode($query, $constraintGroupBuilder, $node->getNodeName());
                }
            }, $this), null, null, $boolean);
        } elseif ($node->getNodeName() === 'not') {
            $builder->where(\Closure::bind(function ($constraintGroupBuilder) use ($node, $boolean) {
                foreach ($node->getQueries() as $query) {
                    $this->visitQueryNode($query, $constraintGroupBuilder, $boolean);
                }
            }, $this), null, null, $boolean.' not');
        } else {
            throw new \LogicException(sprintf('Unknown or unsupported logical node "%s"', $node->getNodeName()));
        }
    }
}
