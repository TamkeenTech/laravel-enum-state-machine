<?php

namespace TamkeenTech\LaravelEnumStateMachine\Providers;

use Illuminate\Support\ServiceProvider;
use TamkeenTech\LaravelEnumStateMachine\Commands\GenerateStatusFlowDiagram;

class LaravelEnumStateMachinesProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (!(bool)glob(base_path('database/migrations/*_create_state_histories_table.php'))) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/create_state_histories_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_state_histories_table.php'),
                ], 'enum-state-machine-migrations');
            }

            $this->publishes([
                __DIR__.'/../../config/enum-diagram.php' => config_path('enum-diagram.php'),
            ], 'enum-state-machine-configs');

            $this->commands([
                GenerateStatusFlowDiagram::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/enum-diagram.php', 'enum-diagram'
        );
    }
}
