<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 11/02/16
 * Time: 23:03.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Repository;

use Illuminate\Database\Eloquent\Model;
use NilPortugues\Foundation\Infrastructure\Model\Repository\Eloquent\EloquentRepository as Repository;

/**
 * Class Repository.
 */
class EloquentRepository extends Repository
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * EloquentRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->modelClass = get_class($model);
    }

    /**
     * {@inheritdoc}
     */
    protected function modelClassName()
    {
        return $this->modelClass;
    }
}
