<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use TamkeenTech\LaravelEnumStateMachine\Traits\StateMachine;

enum EmptyInitialStateEnum: string
{
    use StateMachine;

    case DRAFT = 'draft';
    case PENDING = 'pending';

    public function transitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::PENDING],
            default => []
        };
    }

    public function initialState(): array
    {
        return []; // Empty initial state array
    }
}
