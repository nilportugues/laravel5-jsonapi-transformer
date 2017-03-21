<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 11/14/15
 * Time: 12:24 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\App\Transformers;

use NilPortugues\Api\Mappings\JsonApiMapping;
use NilPortugues\Tests\App\Models\Orders;

/**
 * Class OrdersTransformer.
 */
class OrdersTransformer implements JsonApiMapping
{
    /**
     * Returns a string with the full class name, including namespace.
     *
     * @return string
     */
    public function getClass()
    {
        return Orders::class;
    }

    /**
     * Returns a string representing the resource name as it will be shown after the mapping.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'order';
    }

    /**
     * Returns an array of properties that will be renamed.
     * Key is current property from the class. Value is the property's alias name.
     *
     * @return array
     */
    public function getAliasedProperties()
    {
        return [];
    }

    /**
     * List of properties in the class that will be ignored by the mapping.
     *
     * @return array
     */
    public function getHideProperties()
    {
        return [
        ];
    }

    /**
     * Returns an array of properties that are used as an ID value.
     *
     * @return array
     */
    public function getIdProperties()
    {
        return ['id'];
    }

    /**
     * Returns a list of URLs. This urls must have placeholders to be replaced with the getIdProperties() values.
     *
     * @return array
     */
    public function getUrls()
    {
        return [
            'self' => ['name' => 'orders.show', 'as_id' => 'id'],
            'employee' => ['name' => 'employees.show', 'as_id' => 'employee_id'],
        ];
    }

    /**
     * Returns an array containing the relationship mappings as an array.
     * Key for each relationship defined must match a property of the mapped class.
     *
     * @return array
     */
    public function getRelationships()
    {
        return [];
    }

    public function getRequiredProperties()
    {
        return [];
    }
}
