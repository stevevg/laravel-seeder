<?php

namespace Eighty8\LaravelSeeder\Command;

use File;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class SeedReset extends AbstractSeedMigratorCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback all database seeders';

    /**
     * Execute the console command.
     */
    public function fire(): void
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        // Prepare the migrator.
        $this->prepareMigrator();

        // Reset the migrator.
        $this->info('Removing seeded data for ' . ucfirst($this->getEnvironment()) . ' environment...');
        $this->migrator->reset($this->getMigrationPaths(), $this->getMigrationOptions());

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }

        $this->info('Removed seeded data for ' . ucfirst($this->getEnvironment()) . ' environment');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['env', null, InputOption::VALUE_OPTIONAL, 'The environment to use for the seeders.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
