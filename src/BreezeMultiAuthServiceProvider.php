<?php

namespace Painless\BreezeMultiAuth;


use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BreezeMultiAuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::mixin(new MultiAuthRouteMethods());

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Console\InstallCommand::class];
    }
}
