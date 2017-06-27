<?php

namespace Eighty8\LaravelSeeder\Migration;

use App;
use Config;
use Eighty8\LaravelSeeder\Repository\SeederRepositoryInterface;
use File;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

class SeederMigrator extends Migrator implements SeederMigratorInterface
{
    /**
     * The migration repository implementation.
     *
     * @var SeederRepositoryInterface
     */
    protected $repository;

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The connection resolver instance.
     *
     * @var ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the default connection.
     *
     * @var string
     */
    protected $connection;

    /**
     * The notes for the current operation.
     *
     * @var array
     */
    protected $notes = [];

    /**
     * The paths to all of the migration files.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Create a new migrator instance.
     *
     * @param SeederRepositoryInterface $repository
     * @param ConnectionResolverInterface $resolver
     * @param Filesystem $files
     */
    public function __construct(
        SeederRepositoryInterface $repository,
        ConnectionResolverInterface $resolver,
        Filesystem $files
    ) {
        parent::__construct($repository, $resolver, $files);
    }

    /**
     * Set the environment to run the seeds against.
     *
     * @param $env
     */
    public function setEnvironment(string $env): void
    {
        $this->repository->setEnvironment($env);
    }

    /**
     * Gets the environment the seeds are ran against.
     *
     * @return string|null
     */
    public function getEnvironment(): ?string
    {
        return $this->repository->getEnvironment();
    }

    /**
     * Determines whether an environment has been set.
     *
     * @return bool
     */
    public function hasEnvironment(): bool
    {
        return $this->repository->hasEnvironment();
    }

    /**
     * Run the pending migrations at a given path.
     *
     * @param array $paths
     * @param array $options
     *
     * @return array
     */
    public function run($paths = [], array $options = [])
    {
        // Resolve the environment if one isn't set
        if (!$this->hasEnvironment()) {
            $this->setEnvironment($this->resolveEnvironment());
        }

        return parent::run($paths, $options);
    }

    /**
     * Run "up" a migration instance.
     *
     * @param  string $file
     * @param  int $batch
     * @param  bool $pretend
     */
    protected function runUp($file, $batch, $pretend): void
    {
        // First we will resolve a "real" instance of the seeder class from this
        // seeder file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $seeder = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            $this->pretendToRun($seeder, 'run');

            return;
        }

        $this->note("<comment>Seeding:</comment> {$name}");

        $seeder->run();

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a seeder
        // in the application. A seeder repository keeps the migrate order.
        $this->repository->log($name, $batch);

        $this->note("<info>Seeded:</info> $name");
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string $file
     *
     * @return MigratableSeeder
     */
    public function resolve($file): MigratableSeeder
    {
        return parent::resolve($file);
    }

    /**
     * Run "down" a migration instance.
     *
     * @param  string $file
     * @param  object $migration
     * @param  bool $pretend
     */
    protected function runDown($seed, $migration, $pretend): void
    {
        $file = $seed->seed;

        // First we will get the file name of the migration so we can resolve out an
        // seeder of the migration. Once we get an seeder we can either run a
        // pretend execution of the migration or we can run the real migration.
        $seeder = $this->resolve($file);

        if ($pretend) {
            $this->pretendToRun($seeder, 'down');

            return;
        }

        if (method_exists($seeder, 'down')) {
            $seeder->down();
        }

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
        $this->repository->delete($seed);

        $this->note("<info>Rolled back:</info> $file");
    }

    /**
     * Resolves the application's environment.
     *
     * @return string
     */
    protected function resolveEnvironment(): string
    {
        return App::environment();
    }
}
