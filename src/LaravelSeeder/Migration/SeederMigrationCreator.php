<?php

namespace Eighty8\LaravelSeeder\Migration;

use Illuminate\Database\Migrations\MigrationCreator;
use InvalidArgumentException;

class SeederMigrationCreator extends MigrationCreator
{
    const STUB_PATH = __DIR__ . '/../../../stubs';
    const STUB_FILE = 'MigratableSeeder.stub';

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string $model
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($model): void
    {
        if (class_exists($className = $this->getClassName($model))) {
            throw new InvalidArgumentException("A {$className} seeder already exists.");
        }
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string $model
     *
     * @return string
     */
    protected function getClassName($model): string
    {
        return ucwords($model) . 'Seeder';
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string $model
     * @param  string $stub
     * @param  string $table
     *
     * @return string
     */
    protected function populateStub($model, $stub, $table): string
    {
        $stub = str_replace('{{class}}', $this->getClassName($model), $stub);
        $stub = str_replace('{{model}}', $model, $stub);

        return $stub;
    }

    /**
     * Get the migration stub file.
     *
     * @param  string $table
     * @param  bool $create
     *
     * @return string
     */
    protected function getStub($table, $create): string
    {
        return $this->files->get($this->stubPath() . DIRECTORY_SEPARATOR . self::STUB_FILE);
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath(): string
    {
        return self::STUB_PATH;
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string $name
     * @param  string $path
     *
     * @return string
     */
    protected function getPath($name, $path): string
    {
        return $path . DIRECTORY_SEPARATOR . $this->getDatePrefix() . '_' . $this->getClassName($name) . '.php';
    }
}
