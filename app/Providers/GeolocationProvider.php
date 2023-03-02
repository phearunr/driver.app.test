<?php

namespace App\Providers;

use App\Services\Map\Map;
use App\Services\Geolcation\Geolocation;
use App\Services\Salellite\Salellite;
use Illuminate\Support\ServiceProvider;

class GeolocationProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Geolocation::class, function ($app) {
            $map = new Map();
            $satellite = new Salellite();
            return new Geolocation($map, $satellite);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
