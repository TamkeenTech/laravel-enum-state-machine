<?php

namespace TamkeenTech\LaravelEnumStateMachine\Traits;

use Arr;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\StateEnumDoesntExistException;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\InitailStateIsNotAllowedException;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\StateTransitionNotAllowedException;
use TamkeenTech\LaravelEnumStateMachine\Models\StateHistory;

/**
 * Trait HasStateMachines
 * @package TamkeenTech\LaravelEnumStateMachine\Traits
 * @property array $stateMachines
 */
trait HasStateMachines
{
    protected static function booted()
    {
        parent::boot();

        static::creating(function ($model) {
            foreach ($model->getStateMachines() as $stateToCheck) {
                $state = $model->$stateToCheck;

                // check if null
                if (empty($state)) {
                    return;
                }

                // check if enum class doesnt exist
                if (
                    !isset($model->getCasts()[$stateToCheck]) ||
                    !function_exists('enum_exists') ||
                    !enum_exists($model->getCasts()[$stateToCheck])
                ) {
                    throw new StateEnumDoesntExistException("You need to define enum for your variable `{$stateToCheck}`");
                }

                if (count($state->initialState()) === 0) {
                    throw new InitailStateIsNotAllowedException("You need to define initial state array");
                }

                if (!$state->inInitialState()) {
                    $string = collect($state->initialState())
                        ->implode('value', ',');
                    throw new InitailStateIsNotAllowedException("Only allowed initial states: " . $string);
                }
            }
        });

        static::updating(function ($model) {
            foreach ($model->getStateMachines() as $stateToCheck) {
                $state = $model->getOriginal($stateToCheck);

                // check if null or not changed
                if (empty($state) || $state === $model->$stateToCheck) {
                    return;
                }

                // check if enum class doesnt exist
                if (
                    !isset($model->getCasts()[$stateToCheck]) ||
                    !function_exists('enum_exists') ||
                    !enum_exists($model->getCasts()[$stateToCheck])
                ) {
                    throw new StateEnumDoesntExistException("You need to define enum for your variable `{$stateToCheck}`");
                }

                if (count($state->transitions()) === 0) {
                    throw new StateTransitionNotAllowedException("You need to define transitions array");
                }

                if (!$state->canTransitTo($model->$stateToCheck)) {
                    $string = collect($state->transitions())
                        ->implode('value', ',');

                    throw new StateTransitionNotAllowedException("Only allowed transition states: " . $string);
                }
            }
        });

        static::created(function ($model) {
            collect($model->getStateMachines())
                ->when($model->getRecordStateHistoryFlag(), function ($collection) use ($model) {
                    $history_records = [];
                    $collection->each(function ($stateToCheck) use ($model, &$history_records) {
                        $old_value = null;
                        $new_value = $model->$stateToCheck;

                        if ($old_value !== $new_value) {
                            $history_records[] = [
                                'from' => $old_value,
                                'to' => $new_value,
                                'field' => $stateToCheck,
                                'model_type' => get_class($model),
                                'model_id' => $model->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    });

                    // check if there are any history to save
                    if (count($history_records)) {
                        StateHistory::insert($history_records);
                    }
                });
        });

        static::updated(function ($model) {
            collect($model->getStateMachines())
                ->when($model->getRecordStateHistoryFlag(), function ($collection) use ($model) {

                    $history_records = [];
                    $collection->each(function ($stateToCheck) use ($model, &$history_records) {
                        $old_value = $model->getOriginal($stateToCheck);
                        $new_value = $model->$stateToCheck;

                        if ($old_value !== $new_value) {
                            $history_records[] = [
                                'from' => $old_value,
                                'to' => $new_value,
                                'field' => $stateToCheck,
                                'model_type' => get_class($model),
                                'model_id' => $model->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    });

                    // check if there are any history to save
                    if (count($history_records)) {
                        StateHistory::insert($history_records);
                    }
                });
        });
    }

    public function stateHistory(): MorphMany
    {
        return $this->morphMany(StateHistory::class, 'model');
    }

    public function getStateMachines()
    {
        return $this->stateMachines ?? [];
    }

    public function getRecordStateHistoryFlag(): bool
    {
        return (bool) $this->recordStateHistory ?? false;
    }
}
