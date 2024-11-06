<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use TamkeenTech\LaravelEnumStateMachine\Traits\HasStateMachines;

class TestModel extends Model
{
    use HasStateMachines;

    protected $guarded = [];

    protected $casts = [
        'status' => StatusEnum::class
    ];

    protected array $stateMachines = ['status'];

    protected bool $recordStateHistory = true;
}
