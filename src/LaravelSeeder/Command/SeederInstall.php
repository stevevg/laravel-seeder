<?php

namespace Eighty8\LaravelSeeder\Command;

use Eighty8\LaravelSeeder\Repository\SeederRepository;
use Eighty8\LaravelSeeder\Repository\SeederRepositoryInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SeederInstall extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seeder:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the Seeder repository';

    /**
     * The repository instance.
     *
     * @var SeederRepositoryInterface
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param SeederRepository $repository
     */
    public function __construct(SeederRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     */
    public function fire(): void
    {
        $this->repository->setSource($this->input->getOption('database'));

        $this->repository->createRepository();

        $this->info('Seeders table created successfully.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
        ];
    }
}
