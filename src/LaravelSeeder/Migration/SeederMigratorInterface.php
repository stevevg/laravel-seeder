<?php

namespace Eighty8\LaravelSeeder\Migration;

interface SeederMigratorInterface
{
    /**
     * Run a single migration at a given path.
     *
     * @param  string $path
     * @param  array $options
     */
    public function runSingleFile(string $path, array $options = []): void;

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
}