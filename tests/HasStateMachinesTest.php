<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\TestModel;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\StatusEnum;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\EmptyInitialStateEnum;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\StateEnumDoesntExistException;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\InitailStateIsNotAllowedException;
use TamkeenTech\LaravelEnumStateMachine\Exceptions\StateTransitionNotAllowedException;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\NoTransitionsEnum;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\NoTransitionsTestModel;

class HasStateMachinesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_model_with_valid_initial_state()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertEquals(StatusEnum::DRAFT, $model->status);
    }

    /** @test */
    public function it_throws_exception_when_enum_is_not_defined()
    {
        $this->expectException(StateEnumDoesntExistException::class);

        $testModel = app(TestModel::class);
        // Remove the status cast to simulate missing enum
        $testModel->mergeCasts([
            'status' => 'object'
        ])->create([
            'status' => StatusEnum::DRAFT
        ]);
    }

    /** @test */
    public function it_does_not_throw_exception_when_state_field_is_null()
    {
        $model = TestModel::create([
            'status' => null
        ]);

        $this->assertInstanceOf(TestModel::class, $model);
        $this->assertNull($model->status);
    }

    /** @test */
    public function it_throws_exception_when_initial_state_array_is_empty()
    {
        $this->expectException(InitailStateIsNotAllowedException::class);
        $this->expectExceptionMessage('You need to define initial state array');

        $testModel = app(TestModel::class);
        $testModel->mergeCasts([
            'status' => EmptyInitialStateEnum::class
        ])->create([
            'status' => EmptyInitialStateEnum::DRAFT
        ]);
    }

    /** @test */
    public function it_throws_exception_when_status_is_not_in_initial_states()
    {
        $this->expectException(InitailStateIsNotAllowedException::class);
        $this->expectExceptionMessage('Only allowed initial states: draft');

        TestModel::create([
            'status' => StatusEnum::PENDING
        ]);
    }

    /** @test */
    public function it_allows_updating_model_with_empty_status()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $model->update([
            'status' => StatusEnum::DRAFT
        ]);

        $this->assertEquals(StatusEnum::DRAFT, $model->status);
    }

    /** @test */
    public function it_throws_exception_when_enum_is_not_defined_on_update()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $this->expectException(StateEnumDoesntExistException::class);

        // Remove the status cast to simulate missing enum
        $model->mergeCasts([
            'status' => 'object'
        ])->update([
            'status' => StatusEnum::PENDING
        ]);
    }

    /** @test */
    public function it_throws_exception_when_transitions_are_not_defined_on_update()
    {
        $model = NoTransitionsTestModel::create([
            'status' => NoTransitionsEnum::DRAFT
        ]);

        $this->expectException(StateTransitionNotAllowedException::class);
        $this->expectExceptionMessage('You need to define transitions array');

        $model->update([
            'status' => NoTransitionsEnum::PENDING
        ]);
    }

    /** @test */
    public function it_throws_exception_when_transition_is_not_allowed_on_update()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $this->expectException(StateTransitionNotAllowedException::class);
        $this->expectExceptionMessage('Only allowed transition states: pending');

        // Attempt to transition to a state that is not allowed
        $model->update([
            'status' => StatusEnum::APPROVED
        ]);
    }

    /** @test */
    public function it_can_update_model_with_valid_transition()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $model->update([
            'status' => StatusEnum::PENDING
        ]);

        $this->assertEquals(StatusEnum::PENDING, $model->status);
        $this->assertDatabaseHas('test_models', [
            'id' => $model->id,
            'status' => StatusEnum::PENDING->value
        ]);
    }

    /** @test */
    public function it_records_state_history_on_create()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $this->assertDatabaseHas('state_machine_histories', [
            'from' => null,
            'to' => StatusEnum::DRAFT->value,
            'field' => 'status',
            'model_type' => TestModel::class,
            'model_id' => $model->id
        ]);
    }

    /** @test */
    public function it_records_state_history_on_update()
    {
        $model = TestModel::create([
            'status' => StatusEnum::DRAFT
        ]);

        $model->update([
            'status' => StatusEnum::PENDING
        ]);

        $this->assertDatabaseHas('state_machine_histories', [
            'from' => StatusEnum::DRAFT->value,
            'to' => StatusEnum::PENDING->value,
            'field' => 'status',
            'model_type' => TestModel::class,
            'model_id' => $model->id
        ]);
    }
}
