<?php

namespace Eighty8\LaravelSeeder;

use App;
use Eighty8\LaravelSeeder\Command\DbSeedOverride;
use Eighty8\LaravelSeeder\Command\SeederInstall;
use Eighty8\LaravelSeeder\Command\SeederMake;
use Eighty8\LaravelSeeder\Command\SeederRefresh;
use Eighty8\LaravelSeeder\Command\SeederReset;
use Eighty8\LaravelSeeder\Command\SeederRollback;
use Eighty8\LaravelSeeder\Command\SeederRun;
use Eighty8\LaravelSeeder\Migrator\SeederMigrator;
use Eighty8\LaravelSeeder\Repository\SeederRepository;
use Eighty8\LaravelSeeder\Repository\SeederRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class SeederServiceProvider extends ServiceProvider
{
    const SEEDERS_CONFIG_PATH = __DIR__ . '/../../config/seeders.php';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boots the service provider.
     */
    public function boot(): void
    {
        $this->publishes([self::SEEDERS_CONFIG_PATH => config_path('seeders.php')]);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(self::SEEDERS_CONFIG_PATH, 'seeders');

        $this->registerRepository();

        $this->registerMigrator();

        $this->registerCommands();
    }

    /**
     * Register the SeederRepository.
     */
    private function registerRepository(): void
    {
        $this->app->singleton(SeederRepository::class, function ($app) {
            return new SeederRepository($app['db'], config('seeders.table'));
        });

        $this->app->bind(SeederRepositoryInterface::class, function ($app) {
            return $app[SeederRepository::class];
        });
    }

    /**
     * Register the SeederMigrator.
     */
    private function registerMigrator(): void
    {
        $this->app->singleton(SeederMigrator::class, function ($app) {
            return new SeederMigrator($app[SeederRepositoryInterface::class], $app['db'], $app['files']);
        });
    }

    /**
     * Registers the Seeder Artisan commands.
     */
    private function registerCommands(): void
    {
        $this->app->bind(DbSeedOverride::class, function ($app) {
            return new DbSeedOverride($app[SeederMigrator::class]);
        });

        $this->app->bind(SeederRun::class, function ($app) {
            return new SeederRun($app[SeederMigrator::class]);
        });

        $this->app->bind(SeederInstall::class, function ($app) {
            return new SeederInstall($app[SeederRepositoryInterface::class]);
        });

        $this->app->bind(SeederMake::class, function () {
            return new SeederMake();
        });

        $this->app->bind(SeederReset::class, function ($app) {
            return new SeederReset($app[SeederMigrator::class]);
        });

        $this->app->bind(SeederRollback::class, function ($app) {
            return new SeederRollback($app[SeederMigrator::class]);
        });

        $this->app->bind(SeederRefresh::class, function () {
            return new SeederRefresh();
        });

        $this->commands([
            DbSeedOverride::class,
            SeederRun::class,
            SeederInstall::class,
            SeederMake::class,
            SeederReset::class,
            SeederRollback::class,
            SeederRefresh::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            SeederRepository::class,
            SeederRepositoryInterface::class,
            SeederMigrator::class,
            DbSeedOverride::class,
            SeederRun::class,
            SeederInstall::class,
            SeederMake::class,
            SeederReset::class,
            SeederRollback::class,
            SeederRefresh::class,
        ];
    }
}
