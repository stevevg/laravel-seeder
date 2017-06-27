<?php

namespace Eighty8\LaravelSeeder\Command;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeederMake extends MigrateMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seeder:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a migratable Seeder class';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'seeder:make {model : The name of the model you wish to seed.}
        {--env= : The environment to create the seeder for.}
        {--path= : The relative path from the base path to generate the seed to.}';

    /**
     * Execute the console command.
     */
    public function fire(): void
    {
        // It's possible for the developer to specify the environment for the seeder.
        // The developer may also specify the path.
        $model = ucfirst(trim($this->argument('model')));

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($model, null, null);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string $model
     * @param  string $table
     * @param  bool $created
     *
     * @return string
     */
    protected function writeMigration($model, $table, $created)
    {
        $message = 'Created Seeder';

        $migrationPath = $this->getMigrationPath();

        if ($env = $this->option('env')) {
            $migrationPath .= DIRECTORY_SEPARATOR . $env;
            $message .= ' for ' . ucfirst($env) . ' environment';
        }

        $migration = $this->creator->create($model, $migrationPath);

        $file = pathinfo($migration, PATHINFO_FILENAME);

        $this->line('<info>' . $message . ':</info>' . " {$file}");

        return $file;
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (!empty($targetPath = $this->option('path'))) {
            return $this->laravel->basePath() . DIRECTORY_SEPARATOR . $targetPath;
        }

        return database_path(config('seeders.dir'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of the model you wish to seed.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'env',
                null,
                InputOption::VALUE_OPTIONAL,
                'The environment to create the seeder for.',
                null
            ],
            [
                'path',
                null,
                InputOption::VALUE_OPTIONAL,
                'The relative path to the base path to generate the seed to.',
                null
            ],
        ];
    }
}
