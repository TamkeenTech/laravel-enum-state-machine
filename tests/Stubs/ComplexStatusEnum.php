<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests\Stubs;

use TamkeenTech\LaravelEnumStateMachine\Traits\StateMachine;

enum ComplexStatusEnum: string
{
    use StateMachine;

    case DRAFT = 'draft';
    case PENDING_REVIEW = 'pending_review';
    case IN_REVIEW = 'in_review';
    case NEEDS_REVISION = 'needs_revision';
    case APPROVED = 'approved';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
    case SUSPENDED = 'suspended';
    case DELETED = 'deleted';

    public function transitions(): array
    {
        return match($this) {
            self::DRAFT => [
                self::PENDING_REVIEW,
                self::DELETED
            ],
            self::PENDING_REVIEW => [
                self::IN_REVIEW,
                self::DRAFT,
                self::DELETED,
                self::SUSPENDED
            ],
            self::IN_REVIEW => [
                self::NEEDS_REVISION,
                self::APPROVED,
                self::SUSPENDED
            ],
            self::NEEDS_REVISION => [
                self::DRAFT,
                self::PENDING_REVIEW,
                self::DELETED
            ],
            self::APPROVED => [
                self::SCHEDULED,
                self::PUBLISHED,
                self::ARCHIVED,
                self::SUSPENDED
            ],
            self::SCHEDULED => [
                self::PUBLISHED,
                self::SUSPENDED
            ],
            self::PUBLISHED => [
                self::ARCHIVED,
                self::SUSPENDED
            ],
            self::ARCHIVED => [
                self::PUBLISHED
            ],
            self::SUSPENDED => [
                self::DRAFT,
                self::DELETED
            ],
            self::DELETED => []
        };
    }

    public function initialState(): array
    {
        return [self::DRAFT, self::PENDING_REVIEW]; // Example: Multiple initial states
    }
}
