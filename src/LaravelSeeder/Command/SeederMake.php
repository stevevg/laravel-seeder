<?php

namespace Eighty8\LaravelSeeder\Command;

use Config;
use File;
use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeederMake extends Command
{
    const MIGRATABLE_SEEDER_STUB_PATH = __DIR__ . '/../../../stubs/MigratableSeeder.stub';

    use DetectsApplicationNamespace;

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
     * Execute the console command.
     */
    public function fire(): void
    {
        // Get parameters from user input
        $env = $this->option('env');
        $path = $this->option('path');
        $model = ucfirst($this->argument('model'));

        // Generates the Seeder class
        $filename = $this->generateSeeder($model, $this->getOutputPath($path, $env));

        // Output message
        $this->printMessage($model, $filename, $env);
    }

    /**
     * Generates the Seeder class.
     *
     * @param string $model
     * @param string $outputPath
     *
     * @return string
     */
    private function generateSeeder(string $model, string $outputPath): string
    {
        // Generate filename
        $createdTimestamp = date('Y_m_d_His');
        $fileName = $outputPath . "/{$createdTimestamp}_{$model}" . 'Seeder.php';

        // Get the MigratableSeeder stub
        $stub = File::get(self::MIGRATABLE_SEEDER_STUB_PATH);

        // Fill in the template
        $namespace = rtrim($this->getAppNamespace(), '\\');
        $stub = str_replace('{{model}}', $model . 'Seeder', $stub);
        $stub = str_replace('{{namespace}}', 'namespace ' . $namespace . ';', $stub);
        $stub = str_replace('{{class}}', $model, $stub);

        // Create file
        File::put($fileName, $stub);

        // Return the filename
        return $fileName;
    }

    /**
     * Gets the output path for the file to be generated
     *
     * @param string|null $path
     * @param string|null $env
     *
     * @return string
     */
    private function getOutputPath(?string $path, ?string $env): string
    {
        // Resolve the path from configuration or parameter
        $path = (empty($path))
            ? database_path(config('seeders.dir'))
            : base_path($path);

        // Check if an environment was passed in
        if (!empty($env)) {
            $path .= "/$env";
        }

        // Ensure the directory exists
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    /**
     * Prints the message.
     *
     * @param string $model
     * @param string|null $env
     */
    private function printMessage(string $model, string $filename, ?string $env): void
    {
        $message = 'Seeder created for ' . $model;

        if (!empty($env)) {
            $message .= ' for ' . $env . ' environment';
        }

        $message .= ': ' . $filename;

        $this->line($message);
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
            ['env', null, InputOption::VALUE_OPTIONAL, 'The environment to seed to.', null],
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
