<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use TamkeenTech\LaravelEnumStateMachine\Traits\StateMachine;

enum StatusEnum: string
{
    use StateMachine;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function transitions(): array
    {
        return match ($this) {
            self::DRAFT => [self::PENDING],
            self::PENDING => [self::APPROVED, self::REJECTED],
            default => []
        };
    }

    public function initialState(): array
    {
        return [self::DRAFT];
    }
}
