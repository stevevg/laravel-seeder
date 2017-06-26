<?php

namespace Eighty8\LaravelSeeder\Migrator;

use Illuminate\Database\Migrations\MigrationCreator;

class SeederMigrationCreator extends MigrationCreator
{
    const STUB_PATH = __DIR__ . '/../../stubs';

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return self::STUB_PATH;
    }
}
