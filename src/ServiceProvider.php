<?php

namespace Guysolamour\Hootsuite;

use Guysolamour\Hootsuite\Clients\HootsuiteClient;
use Guysolamour\Hootsuite\Commands\GetOauthCodeUrlCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const MIGRATIONS_PATH = __DIR__ . '/migrations';
    const CONFIG_PATH     = __DIR__ . '/../config/hootsuite.php';
    const SETTING_PATH    = __DIR__ . '/../config/settings.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH  => config_path('hootsuite.php'),
        ], 'hootsuite-config');

        $this->publishes([
            self::MIGRATIONS_PATH => config('settings.migrations_path'),
        ], 'hootsuite-migrations');

        $this->loadMigrationsFrom(self::MIGRATIONS_PATH);

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->loadHelperFile();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'hootsuite'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                GetOauthCodeUrlCommand::class,
            ]);
        }


        $this->app->bind('hootsuite', function () {
            return new HootsuiteClient;
        });

    }

    private function loadHelperFile()
    {
        require __DIR__ . '/helpers.php';
    }
}
