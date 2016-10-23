<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/9/15
 * Time: 2:57 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\App\Controller;

use NilPortugues\Laravel5\JsonApi\Controller\JsonApiController;
use NilPortugues\Tests\App\Models\Orders;

class OrdersController extends JsonApiController
{
    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getDataModel()
    {
        return new Orders();
    }
}
