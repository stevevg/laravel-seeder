<?php

namespace Eighty8\LaravelSeeder;

use App;
use Eighty8\LaravelSeeder\Commands\DbSeedOverride;
use Eighty8\LaravelSeeder\Commands\SeederInstall;
use Eighty8\LaravelSeeder\Commands\SeederMake;
use Eighty8\LaravelSeeder\Commands\SeederRefresh;
use Eighty8\LaravelSeeder\Commands\SeederReset;
use Eighty8\LaravelSeeder\Commands\SeederRollback;
use Eighty8\LaravelSeeder\Commands\SeederRun;
use Illuminate\Support\ServiceProvider;

class SeederServiceProvider extends ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../../config/seeders.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('seeders.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH, 'seeders'
        );

        $this->app->singleton('seeder.repository', function ($app) {
            return new SeederRepository($app['db'], config('seeders.table'));
        });

        $this->app->singleton('seeder.migrator', function ($app) {
            return new SeederMigrator($app['seeder.repository'], $app['db'], $app['files']);
        });

        $this->app->bind('seeder.run', function ($app) {
            return new SeederRun($app['seeder.migrator']);
        });

        $this->app->bind('seeder.install', function ($app) {
            return new SeederInstall($app['seeder.repository']);
        });

        $this->app->bind('seeder.make', function () {
            return new SeederMake();
        });

        $this->app->bind('seeder.reset', function ($app) {
            return new SeederReset($app['seeder.migrator']);
        });

        $this->app->bind('seeder.rollback', function ($app) {
            return new SeederRollback($app['seeder.migrator']);
        });

        $this->app->bind('seeder.refresh', function () {
            return new SeederRefresh();
        });

        $this->app->bind('seeder.override', function ($app) {
            return new DbSeedOverride($app['seeder.migrator']);
        });

        $this->commands([
            'seeder.run',
            'seeder.install',
            'seeder.make',
            'seeder.reset',
            'seeder.rollback',
            'seeder.refresh',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'seeder.repository',
            'seeder.migrator',
            'seeder.override',
            'seeder.run',
            'seeder.install',
            'seeder.make',
            'seeder.reset',
            'seeder.rollback',
            'seeder.refresh',
        ];
    }
}
