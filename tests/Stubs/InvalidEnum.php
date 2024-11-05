<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

enum InvalidEnum: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
}
