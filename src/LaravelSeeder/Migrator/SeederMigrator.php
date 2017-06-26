<?php

namespace Eighty8\LaravelSeeder\Migrator;

use App;
use Config;
use Eighty8\LaravelSeeder\Repository\SeederRepositoryInterface;
use File;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;

class SeederMigrator extends Migrator
{
    use DetectsApplicationNamespace;

    /**
     * The migration repository implementation.
     *
     * @var SeederRepositoryInterface
     */
    protected $repository;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
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
     * Sets environment.
     *
     * @param string $env
     */
    public function setEnvironment(string $env): void
    {
        $this->repository->setEnvironment($env);
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string $path
     *
     * @return array
     */
    public function getMigrationFiles($path): array
    {
        $files = [];

        if (!$this->repository->hasEnvironment()) {
            $files = array_merge($files, $this->files->glob("$path/{$this->repository->getEnvironment()}/*.php"));
        }

        $files = array_merge($files, $this->files->glob($path . '/*.php'));

        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if ($files === false) {
            return [];
        }

        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);

        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        return $files;
    }

    /**
     * Run the outstanding migrations at a given path.
     *
     * @param  string $path
     * @param  bool $pretend
     */
    public function runSingleFile($path, $pretend = false): void
    {
        $this->notes = [];
        $file = str_replace('.php', '', basename($path));
        $files = [$file];

        // Once we grab all of the migration files for the path, we will compare them
        // against the migrations that have already been run for this package then
        // run each of the outstanding migrations against a database connection.
        $ran = $this->repository->getRan();

        $migrations = array_diff($files, $ran);
        $filename_ext = pathinfo($path, PATHINFO_EXTENSION);

        if (!$filename_ext) {
            $path .= '.php';
        }

        $this->files->requireOnce($path);

        $this->runPending($migrations, $pretend);
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
        $seeder = $this->resolve($file);

        if ($pretend) {
            $this->pretendToRun($seeder, 'run');

            return;
        }

        $seeder->run();

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a seeder
        // in the application. A seeder repository keeps the migrate order.
        $this->repository->log($file, $batch);

        $this->note("<info>Seeded:</info> $file");
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
        $filePath = database_path(config('seeders.dir') . '/' . $file . '.php');

        if (File::exists($filePath)) {
            require_once $filePath;
        } elseif (!empty($this->repository->env)) {
            require_once database_path(config('seeders.dir') . '/' . $this->repository->getEnvironment() . '/' . $file . '.php');
        } else {
            require_once database_path(config('seeders.dir') . '/' . App::environment() . '/' . $file . '.php');
        }

        $fullPath = $this->getAppNamespace() . $file;

        return new $fullPath();
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
}
