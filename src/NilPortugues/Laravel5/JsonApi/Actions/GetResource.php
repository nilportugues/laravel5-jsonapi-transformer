<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 5/04/16
 * Time: 0:17.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Actions;

use Exception;

/**
 * Class GetResource.
 */
class GetResource extends \NilPortugues\Api\JsonApi\Server\Actions\GetResource
{
    /**
     * @param Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws Exception
     */
    public function getErrorResponse(Exception $e)
    {
        if (config('app.debug')) {
            throw $e;
        }

        return parent::getErrorResponse($e);
    }
}
