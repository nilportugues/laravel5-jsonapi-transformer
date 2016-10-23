<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/01/16
 * Time: 0:06.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi\Providers;

use Illuminate\Container\Container;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use ReflectionClass;

/**
 * Class Laravel52Provider.
 */
class Laravel52Provider extends Laravel51Provider
{
    /**
     * @var RouteCollection
     */
    protected $routerCollection;

    /**
     * @param array $value
     *
     * @return mixed|string
     *
     * @throws \Exception
     */
    protected function calculateRoute(array $value)
    {
        $router = Container::getInstance()->make('url');
        $route = '';

        /** @var \Illuminate\Routing\Route $routerObject */
        foreach ($this->getRouterCollection($router) as $routerObject) {
            if ($routerObject->getName() === $value['name']) {
                $route = $routerObject->getPath();

                return $this->calculateFullPath($value, $route);
            }
        }

        if (empty($route)) {
            throw new \Exception('Provided route name does not exist');
        }
    }

    /**
     * @param UrlGenerator $router
     * @return mixed
     */
    protected function getRouterCollection(UrlGenerator $router)
    {
        if (!empty($this->routerCollection)) {
            return $this->routerCollection;
        }

        $reflectionClass = new ReflectionClass($router);
        $reflectionProperty = $reflectionClass->getProperty('routes');
        $reflectionProperty->setAccessible(true);
        $routeCollection = $reflectionProperty->getValue($router);

        $this->routerCollection = $routeCollection;

        return $routeCollection;
    }

    /**
     * @param array        $value
     * @param string       $route
     *
     * @return mixed|string
     */
    protected function calculateFullPath(array &$value, $route)
    {
        if (!empty($value['as_id'])) {
            preg_match_all('/{(.*?)}/', $route, $matches);
            $route = str_replace($matches[0], '{'.$value['as_id'].'}', $route);
        }

        return url($route);
    }
}
