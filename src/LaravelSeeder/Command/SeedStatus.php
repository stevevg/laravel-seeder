<?php

namespace Eighty8\LaravelSeeder\Command;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputOption;

class SeedStatus extends AbstractSeedMigratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each seeder';

    /**
     * Execute the console command.
     */
    public function fire(): void
    {
        // Prepare the migrator.
        $this->prepareMigrator();

        // Get ran seeders.
        $ran = $this->migrator->getRepository()->getRan();

        if (count($migrations = $this->getStatusFor($ran)) > 0) {
            $this->table(['Ran?', 'Migration'], $migrations);
        } else {
            $this->error('No migrations found');
        }
    }

    /**
     * Get the status for the given ran migrations.
     *
     * @param  array $ran
     * @return \Illuminate\Support\Collection
     */
    protected function getStatusFor(array $ran)
    {
        return Collection::make($this->getAllMigrationFiles())
            ->map(function ($migration) use ($ran) {
                $migrationName = $this->migrator->getMigrationName($migration);

                return in_array($migrationName, $ran)
                    ? ['<info>Y</info>', $migrationName]
                    : ['<fg=red>N</fg=red>', $migrationName];
            });
    }

    /**
     * Get an array of all of the migration files.
     *
     * @return array
     */
    protected function getAllMigrationFiles()
    {
        return $this->migrator->getMigrationFiles($this->getMigrationPaths());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['env', null, InputOption::VALUE_OPTIONAL, 'The environment to use for the seeders.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of seeder files to use.'],
        ];
    }
}