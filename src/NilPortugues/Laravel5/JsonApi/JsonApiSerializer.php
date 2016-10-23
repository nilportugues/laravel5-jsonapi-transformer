<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/16/15
 * Time: 4:43 AM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi;

use NilPortugues\Serializer\Drivers\Eloquent\EloquentDriver;

/**
 * Class JsonApiSerializer.
 */
class JsonApiSerializer extends \NilPortugues\Api\JsonApi\JsonApiSerializer
{
    /**
     * Extract the data from an object.
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function serializeObject($value)
    {
        $serialized = EloquentDriver::serialize($value);

        return ($value !== $serialized) ? $serialized : parent::serializeObject($value);
    }
}
