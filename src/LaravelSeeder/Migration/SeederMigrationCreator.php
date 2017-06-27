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
    protected function ensureMigrationDoesntAlreadyExist($model)
    {
        if (class_exists($className = $this->getClassName($model))) {
            throw new InvalidArgumentException("A {$className} seeder already exists.");
        }
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
    protected function populateStub($model, $stub, $table)
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
    protected function getStub($table, $create)
    {
        return $this->files->get($this->stubPath() . DIRECTORY_SEPARATOR . self::STUB_FILE);
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string $model
     * @return string
     */
    protected function getClassName($model)
    {
        return ucwords($model) . 'Seeder';
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
}
