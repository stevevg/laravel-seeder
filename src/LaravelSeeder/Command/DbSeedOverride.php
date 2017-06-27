<?php

namespace Eighty8\LaravelSeeder\Command;

use Eighty8\LaravelSeeder\Migration\SeederMigrator;
use Symfony\Component\Console\Input\InputOption;

class DbSeedOverride extends SeederRun
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'db:seed';

    /**
     * Constructor.
     *
     * @param SeederMigrator $migrator
     */
    public function __construct(SeederMigrator $migrator)
    {
        parent::__construct($migrator);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $options = parent::getOptions();

        $options[] = [
            'class',
            null,
            InputOption::VALUE_OPTIONAL,
            "This override Laravel's behavior.",
            null
        ];

        return $options;
    }
}
