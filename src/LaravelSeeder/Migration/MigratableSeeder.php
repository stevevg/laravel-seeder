<?php

namespace Eighty8\LaravelSeeder\Migration;

use Illuminate\Database\Seeder;

abstract class MigratableSeeder extends Seeder
{

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * Get the migration connection name.
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Run the database seeder.
     */
    public abstract function run(): void;

    /**
     * Reverses the database seeder.
     */
    public abstract function down(): void;
}