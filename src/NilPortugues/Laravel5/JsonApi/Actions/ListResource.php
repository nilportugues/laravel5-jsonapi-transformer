<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 5/04/16
 * Time: 0:15.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Actions;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use NilPortugues\Api\JsonApi\Http\PaginatedResource;
use NilPortugues\Api\JsonApi\Server\Errors\ErrorBag;
use NilPortugues\Api\JsonApi\Server\Errors\OufOfBoundsError;
use NilPortugues\Api\JsonApi\Server\Query\QueryObject;

/**
 * Class ListResource.
 */
class ListResource extends \NilPortugues\Api\JsonApi\Server\Actions\ListResource
{
    /**
     * @param \Exception $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function getErrorResponse(\Exception $e)
    {
        if (config('app.debug')) {
            throw $e;
        }

        return parent::getErrorResponse($e);
    }
    
}
