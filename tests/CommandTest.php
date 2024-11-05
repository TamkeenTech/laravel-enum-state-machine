<?php

namespace TamkeenTech\LaravelEnumStateMachine\Tests;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\StatusEnum;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\InvalidEnum;
use TamkeenTech\LaravelEnumStateMachine\Commands\GenerateStatusFlowDiagram;
use TamkeenTech\LaravelEnumStateMachine\Tests\Stubs\ComplexStatusEnum;

class CommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure clean state before each test
        File::deleteDirectory(storage_path('app/flows'));
        File::deleteDirectory(storage_path('app/custom'));
    }

    protected function tearDown(): void
    {
        // Cleanup after each test
        File::deleteDirectory(storage_path('app/flows'));
        File::deleteDirectory(storage_path('app/custom'));
        parent::tearDown();
    }

    /** @test */
    public function it_can_generate_diagram_successfully()
    {
        Process::shouldReceive('run')->andReturnSelf();
        Process::shouldReceive('successful')->andReturn(true);

        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => StatusEnum::class])
            ->assertExitCode(0);
    }

    /** @test */
    public function it_fails_when_enum_doesnt_implement_transitions_method()
    {
        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => InvalidEnum::class])
            ->expectsOutputToContain('You need to implement the transitions method')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_fails_when_enum_class_does_not_exist()
    {
        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => 'NonExistentEnum'])
            ->expectsOutputToContain('Enum class NonExistentEnum not found.')
            ->assertExitCode(1);
    }

    /** @test */
    public function it_generates_file_in_default_output_path()
    {
        $defaultPath = config('enum-diagram.output_directory');
        $expectedPath = "{$defaultPath}/StatusEnum.png";

        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => StatusEnum::class])
            ->expectsOutputToContain("Flow diagram generated successfully at {$expectedPath}")
            ->assertExitCode(0);

        $this->assertFileExists($expectedPath);
    }

    /** @test */
    public function it_generates_file_in_custom_output_path()
    {
        $customPath = storage_path('app/custom/path');
        $expectedPath = "{$customPath}/StatusEnum.png";

        $this->artisan(GenerateStatusFlowDiagram::class, [
            'enum' => StatusEnum::class,
            '--output' => $customPath
        ])
            ->expectsOutputToContain("Flow diagram generated successfully at {$expectedPath}")
            ->assertExitCode(0);

        $this->assertFileExists($expectedPath);
    }

    /** @test */
    public function it_generates_file_with_correct_name()
    {
        $defaultPath = config('enum-diagram.output_directory');
        $expectedPath = "{$defaultPath}/StatusEnum.png";
        $wrongPath = "{$defaultPath}/WrongName.png";

        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => StatusEnum::class])
            ->expectsOutputToContain("Flow diagram generated successfully at {$expectedPath}")
            ->assertExitCode(0);

        $this->assertFileExists($expectedPath);
        $this->assertFileDoesNotExist($wrongPath);
    }

    /** @test */
    public function it_generates_complex_status_diagram_successfully()
    {
        $defaultPath = config('enum-diagram.output_directory');
        $expectedPath = "{$defaultPath}/ComplexStatusEnum.png";

        $this->artisan(GenerateStatusFlowDiagram::class, ['enum' => ComplexStatusEnum::class])
            ->expectsOutputToContain("Flow diagram generated successfully at {$expectedPath}")
            ->assertExitCode(0);

        $this->assertFileExists($expectedPath);

        // Verify the generated file has content (not empty)
        $this->assertGreaterThan(0, filesize($expectedPath));
    }
}
