<?php

/*
 * This file is part of Laravel PayPal.
 *
 * (c) Brian Faust <hello@brianfaust.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BrianFaust\PayPal;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class PayPalServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $source = realpath(__DIR__.'/../config/paypal.php');

        $this->publishes([$source => config_path('paypal.php')]);

        $this->mergeConfigFrom($source, 'paypal');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the factory class.
     */
    protected function registerFactory()
    {
        $this->app->singleton('paypal.factory', function () {
            return new PayPalFactory();
        });

        $this->app->alias('paypal.factory', PayPalFactory::class);
    }

    /**
     * Register the manager class.
     */
    protected function registerManager()
    {
        $this->app->singleton('paypal', function (Container $app) {
            $config = $app['config'];
            $factory = $app['paypal.factory'];

            return new PayPalManager($config, $factory);
        });

        $this->app->alias('paypal', PayPalManager::class);
    }

    /**
     * Register the bindings.
     */
    protected function registerBindings()
    {
        $this->app->bind('paypal.connection', function (Container $app) {
            $manager = $app['paypal'];

            return $manager->connection();
        });

        $this->app->alias('paypal.connection', PayPal::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'paypal',
            'paypal.factory',
            'paypal.connection',
        ];
    }
}
