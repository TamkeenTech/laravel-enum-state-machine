<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use TamkeenTech\LaravelEnumStateMachine\Providers\LaravelEnumStateMachinesProvider;

class TestCase extends BaseTestCase
{
    use WithWorkbench;

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelEnumStateMachinesProvider::class,
        ];
    }
}
