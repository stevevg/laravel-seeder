<?php

namespace Eighty8\LaravelSeeder;

use Illuminate\Database\Migrations\MigrationCreator;

class SeederMigrationCreator extends MigrationCreator
{
    const STUBS_PATH = __DIR__ . '/../../stubs';

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return self::STUBS_PATH;
    }
}
