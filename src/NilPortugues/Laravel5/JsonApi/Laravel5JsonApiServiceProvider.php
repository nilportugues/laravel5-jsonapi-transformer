<?php

/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 8/15/15
 * Time: 5:45 PM.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Laravel5\JsonApi;

use Illuminate\Support\ServiceProvider;
use NilPortugues\Laravel5\JsonApi\Providers\Laravel51Provider;
use NilPortugues\Laravel5\JsonApi\Providers\Laravel52Provider;
use NilPortugues\Laravel5\JsonApi\Providers\Laravel53Provider;

class Laravel5JsonApiServiceProvider extends ServiceProvider
{
    const LARAVEL_APPLICATION = 'Illuminate\Foundation\Application';
    const PATH = '/../../../config/jsonapi.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../../../config') => base_path('config')]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.self::PATH, 'jsonapi');

        $version = '5.0.0';
        if (class_exists(self::LARAVEL_APPLICATION, true)) {
            $class = self::LARAVEL_APPLICATION;
            $version = $class::VERSION;
        }

        switch ($version) {
            case false !== strpos($version, '5.0.'):
            case false !== strpos($version, '5.1.'):
                $provider = new Laravel51Provider();
                break;
            case false !== strpos($version, '5.2.'):
                $provider = new Laravel52Provider();
                break;
            case false !== strpos($version, '5.3.'):
                $provider = new Laravel53Provider();
                break;
            default:
                throw new \RuntimeException(
                    sprintf('Laravel version %s is not supported. Please use the 5.1 for the time being', $version)
                );
                break;
        }

        $this->app->singleton(JsonApiSerializer::class, $provider->provider());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['jsonapi'];
    }
}
