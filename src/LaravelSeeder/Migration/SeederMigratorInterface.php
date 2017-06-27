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
}