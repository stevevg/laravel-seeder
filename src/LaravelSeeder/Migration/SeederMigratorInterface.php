<?php

namespace Eighty8\LaravelSeeder\Migration;

interface SeederMigratorInterface
{
    /**
     * Set the environment to run the seeds against.
     *
     * @param $env
     */
    public function setEnvironment(string $env): void;

    /**
     * Gets the environment the seeds are ran against.
     *
     * @return string|null
     */
    public function getEnvironment(): ?string;

    /**
     * Determines whether an environment has been set.
     *
     * @return bool
     */
    public function hasEnvironment(): bool;

    /**
     * Run the pending migrations at a given path.
     *
     * @param array $paths
     * @param array $options
     *
     * @return array
     */
    public function run($paths = [], array $options = []): array;

    /**
     * Run an array of migrations.
     *
     * @param  array $migrations
     * @param  array $options
     */
    public function runPending(array $migrations, array $options = []): void;

    /**
     * Rolls all of the currently applied migrations back.
     *
     * @param  array|string $paths
     * @param  bool $pretend
     *
     * @return array
     */
    public function reset($paths = [], $pretend = false): array;

    /**
     * Rollback the last migration operation.
     *
     * @param  array|string $paths
     * @param  array $options
     *
     * @return array
     */
    public function rollback($paths = [], array $options = []): array;

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string $file
     *
     * @return MigratableSeeder
     */
    public function resolve($file): MigratableSeeder;

    /**
     * Get the notes for the last operation.
     *
     * @return array
     */
    public function getNotes(): array;

    /**
     * Set the default connection name.
     *
     * @param  string $name
     */
    public function setConnection($name): void;

    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists(): bool;
}