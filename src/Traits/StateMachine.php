<?php

namespace TamkeenTech\LaravelEnumStateMachine\Traits;

/**
 * Trait StateMachine
 * @package TamkeenTech\LaravelEnumStateMachine\Traits
 */
trait StateMachine
{
    public function transitions(): array
    {
        return [];
    }

    public function initialState(): array
    {
        return [];
    }

    public function canTransitTo(self $status): bool
    {
        return in_array($status, $this->transitions());
    }

    public function inInitialState(): bool
    {
        return in_array($this, $this->initialState());
    }

    public function is(self $status): bool
    {
        return $this === $status;
    }
}
