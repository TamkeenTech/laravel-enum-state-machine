<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use TamkeenTech\LaravelEnumStateMachine\Traits\HasStateMachines;

class NoTransitionsTestModel extends Model
{
    use HasStateMachines;

    protected $table = 'test_models';

    protected $guarded = [];

    protected $casts = [
        'status' => NoTransitionsEnum::class
    ];

    protected array $stateMachines = ['status'];
}
