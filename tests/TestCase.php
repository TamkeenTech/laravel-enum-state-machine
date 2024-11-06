<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\Attributes\WithEnv;
use Orchestra\Testbench\TestCase as Orchestra;
use TamkeenTech\LaravelEnumStateMachine\Providers\LaravelEnumStateMachinesProvider;

#[WithEnv('DB_CONNECTION', 'sqlite')]
abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelEnumStateMachinesProvider::class,
        ];
    }

    protected function setUpDatabase()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status')->nullable();
            $table->timestamps();
        });

        $this->app['db']->connection()->getSchemaBuilder()->create('state_machine_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('field');
            $table->morphs('model');
            $table->timestamps();
        });
    }
}
