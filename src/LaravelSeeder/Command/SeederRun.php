<?php

namespace Eighty8\LaravelSeeder\Command;

use Eighty8\LaravelSeeder\Migration\SeederMigrator;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class SeederRun extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seeder:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds the database';

    /**
     * SeederMigrator.
     *
     * @var SeederMigrator
     */
    private $migrator;

    /**
     * Constructor.
     *
     * @param SeederMigrator $migrator
     */
    public function __construct(SeederMigrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     */
    public function fire(): void
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        // Get options from user input
        $env = $this->option('env');

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $options = [
            'pretend' => $this->input->getOption('pretend'),
        ];

        // Prepare the migrator
        $this->prepareMigrator($env);

        // Get the path for the migrations
        $this->migrator->run($this->getMigrationPaths($env), $options);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }
    }

    /**
     * Prepare the migration database for running.
     *
     * @param string|null $env
     *
     * @return void
     */
    protected function prepareMigrator(?string $env): void
    {
        // Set the connection for the migrator
        $this->migrator->setConnection($this->input->getOption('database'));

        // Create the seeder migration table if it doesn't already exist
        if (!$this->migrator->repositoryExists()) {
            $options = [
                '--database' => $this->input->getOption('database'),
            ];

            $this->call('seeder:install', $options);
        }

        // Set the environment if there is one
        if ($env) {
            $this->migrator->setEnvironment($env);
        }
    }

    /**
     * Gets all seeder migration paths.
     *
     * @param string|null $env
     *
     * @return array
     */
    private function getMigrationPaths(?string $env): array
    {
        $paths = [];

        $seedersPath = database_path(config('seeders.dir'));

        if ($env) {
            $paths[] = $seedersPath . DIRECTORY_SEPARATOR . $env;
        }

        return $paths;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['env', null, InputOption::VALUE_OPTIONAL, 'The environment in which to run the seeds.', null],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
