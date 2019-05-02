<?php

namespace Emanuti\Granatum;

use Illuminate\Support\ServiceProvider;

class GranatumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/src/config/granatum.php' => config_path('granatum.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Granatum', function () {
            return new Granatum;
        });
    }
}
