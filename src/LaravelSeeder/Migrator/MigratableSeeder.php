<?php

namespace Eighty8\LaravelSeeder\Migrator;

interface MigratableSeeder
{
    public function run(): void;

    public function down(): void;
}