<?php

namespace Wqer\IpLimit;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Wqer\IpLimit\Middlewares\IpLimit;

class IpLimitServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom($this->configPath(), 'iplimit');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->app->singleton(IpLimitService::class, function ($app) {
            return new IpLimitService($app['config']->get('iplimit'));
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([$this->configPath() => config_path('iplimit.php')]);

        /** @var \Illuminate\Foundation\Http\Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        // When the IpLimit middleware is not attached globally, add the PreflightCheck
        if (! $kernel->hasMiddleware(IpLimit::class)) {
            $kernel->prependMiddleware(IpLimit::class);
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/iplimit.php';
    }
}
