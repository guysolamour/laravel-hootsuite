<?php

namespace Guysolamour\Hootsuite;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/hootsuite.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('hootsuite.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'hootsuite'
        );

        $this->app->bind('hootsuite', function () {
            return new Hootsuite();
        });

        $this->loadHelperFile();

    }

    private function loadHelperFile()
    {
        require __DIR__ . '/helpers.php';
    }
}
