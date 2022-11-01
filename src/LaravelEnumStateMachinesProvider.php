<?php
namespace TamkeenTech\LaravelEnumStateMachine;

use Illuminate\Support\ServiceProvider;

class LaravelEnumStateMachinesProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_state_histories_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_state_histories_table.php'),
            ], 'enum-state-machine-migrations');
        }
    }

    public function register()
    {
        //
    }
}