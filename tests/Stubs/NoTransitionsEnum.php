<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use TamkeenTech\LaravelEnumStateMachine\Traits\StateMachine;

enum NoTransitionsEnum: string
{
    use StateMachine;

    case DRAFT = 'draft';
    case PENDING = 'pending';

    public function initialState(): array
    {
        return [self::DRAFT];
    }
}
