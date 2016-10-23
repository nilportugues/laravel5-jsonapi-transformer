<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 13/01/16
 * Time: 19:57.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Controller;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Controller;

/**
 * Class LumenJsonApiController.
 */
abstract class LumenJsonApiController extends Controller
{
    use JsonApiTrait;

    /**
     * @param string $controllerAction
     *
     * @return mixed
     *
     *
     * Add the missing implementation by using this as inspiration:
     * https://gist.github.com/radmen/92200c62b633320b98a8
     */
    protected function uriGenerator($controllerAction)
    {
        /** @var array $routes */
        $routes = Application::getInstance()->getRoutes();
        foreach ($routes as $route) {
            if ($route['action'] === $controllerAction) {
                return $route;
            }
        }
    }
}
