<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests;

use PHPUnit\Framework\TestCase;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\StatusEnum;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\ComplexStatusEnum;

class StateMachineTest extends TestCase
{
    /** @test */
    public function it_returns_correct_transitions_for_draft_status()
    {
        // Arrange
        $status = StatusEnum::DRAFT;

        // Act
        $transitions = $status->transitions();

        // Assert
        $this->assertEquals(
            [StatusEnum::PENDING],
            $transitions,
            'DRAFT status should only transition to PENDING'
        );
    }

    /** @test */
    public function it_returns_correct_multiple_transitions_for_pending_review_status()
    {
        // Arrange
        $status = ComplexStatusEnum::PENDING_REVIEW;

        // Act
        $transitions = $status->transitions();

        // Assert
        $expectedTransitions = [
            ComplexStatusEnum::IN_REVIEW,
            ComplexStatusEnum::DRAFT,
            ComplexStatusEnum::DELETED,
            ComplexStatusEnum::SUSPENDED
        ];

        $this->assertEquals(
            $expectedTransitions,
            $transitions,
            'PENDING_REVIEW status should transition to multiple statuses'
        );

        $this->assertCount(
            4,
            $transitions,
            'PENDING_REVIEW status should have exactly 4 possible transitions'
        );
    }

    /** @test */
    public function it_returns_correct_initial_states()
    {
        // Assert initial state for StatusEnum
        $this->assertEquals(
            [StatusEnum::DRAFT],
            StatusEnum::DRAFT->initialState(),
            'StatusEnum should have DRAFT as initial state'
        );

        // Assert multiple initial states for ComplexStatusEnum
        $this->assertEquals(
            [ComplexStatusEnum::DRAFT, ComplexStatusEnum::PENDING_REVIEW],
            ComplexStatusEnum::DRAFT->initialState(),
            'ComplexStatusEnum should have DRAFT and PENDING_REVIEW as initial states'
        );
    }

    /** @test */
    public function it_validates_valid_status_transitions()
    {
        // Test valid transitions
        $this->assertTrue(
            StatusEnum::DRAFT->canTransitTo(StatusEnum::PENDING),
            'DRAFT should be able to transit to PENDING'
        );

        $this->assertTrue(
            StatusEnum::PENDING->canTransitTo(StatusEnum::APPROVED),
            'PENDING should be able to transit to APPROVED'
        );

        $this->assertTrue(
            ComplexStatusEnum::PENDING_REVIEW->canTransitTo(ComplexStatusEnum::IN_REVIEW),
            'PENDING_REVIEW should be able to transit to IN_REVIEW'
        );
    }

    /** @test */
    public function it_validates_invalid_status_transitions()
    {
        // Test invalid transitions
        $this->assertFalse(
            StatusEnum::DRAFT->canTransitTo(StatusEnum::APPROVED),
            'DRAFT should not be able to transit to APPROVED directly'
        );

        $this->assertFalse(
            StatusEnum::APPROVED->canTransitTo(StatusEnum::DRAFT),
            'APPROVED should not be able to transit back to DRAFT'
        );

        $this->assertFalse(
            ComplexStatusEnum::DELETED->canTransitTo(ComplexStatusEnum::DRAFT),
            'DELETED should not be able to transit to any status'
        );
    }

    /** @test */
    public function it_validates_initial_state_correctly()
    {
        // Test initial state validation for StatusEnum
        $this->assertTrue(
            StatusEnum::DRAFT->inInitialState(),
            'DRAFT should be recognized as initial state'
        );

        $this->assertFalse(
            StatusEnum::PENDING->inInitialState(),
            'PENDING should not be recognized as initial state'
        );

        // Test initial state validation for ComplexStatusEnum
        $this->assertTrue(
            ComplexStatusEnum::DRAFT->inInitialState(),
            'DRAFT should be recognized as initial state in ComplexStatusEnum'
        );

        $this->assertTrue(
            ComplexStatusEnum::PENDING_REVIEW->inInitialState(),
            'PENDING_REVIEW should be recognized as initial state in ComplexStatusEnum'
        );

        $this->assertFalse(
            ComplexStatusEnum::IN_REVIEW->inInitialState(),
            'IN_REVIEW should not be recognized as initial state in ComplexStatusEnum'
        );
    }

    /** @test */
    public function it_correctly_compares_status_equality()
    {
        // Test status equality
        $this->assertTrue(
            StatusEnum::DRAFT->is(StatusEnum::DRAFT),
            'Same status should be equal'
        );

        $this->assertFalse(
            StatusEnum::DRAFT->is(StatusEnum::PENDING),
            'Different statuses should not be equal'
        );

        // Test complex status equality
        $this->assertTrue(
            ComplexStatusEnum::PENDING_REVIEW->is(ComplexStatusEnum::PENDING_REVIEW),
            'Same complex status should be equal'
        );

        $this->assertFalse(
            ComplexStatusEnum::PENDING_REVIEW->is(ComplexStatusEnum::IN_REVIEW),
            'Different complex statuses should not be equal'
        );
    }

    /** @test */
    public function it_handles_terminal_states_correctly()
    {
        // Test terminal states (states with no transitions)
        $this->assertEmpty(
            StatusEnum::APPROVED->transitions(),
            'APPROVED should be a terminal state with no transitions'
        );

        $this->assertEmpty(
            ComplexStatusEnum::DELETED->transitions(),
            'DELETED should be a terminal state with no transitions'
        );
    }
}
