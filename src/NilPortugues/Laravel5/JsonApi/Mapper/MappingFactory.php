<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 10/16/15
 * Time: 8:59 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Mapper;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;

/**
 * Class MappingFactory.
 */
class MappingFactory extends \NilPortugues\Api\Mapping\MappingFactory
{
    /**
     * @var array
     */
    protected static $eloquentClasses = [];

    /**
     * @param string $className
     *
     * @return array
     */
    protected static function getClassProperties($className)
    {
        if (\class_exists($className, true)) {
            $reflection = new ReflectionClass($className);
            $value = $reflection->newInstanceWithoutConstructor();

            if (\is_subclass_of($value, Model::class, true)) {
                $attributes = Schema::getColumnListing($value->getTable());

                self::$eloquentClasses[$className] = $attributes;
            }
        }

        return (!empty(self::$eloquentClasses[$className])) ? self::$eloquentClasses[$className] : parent::getClassProperties($className);
    }
}
