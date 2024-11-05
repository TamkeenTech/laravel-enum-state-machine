<?php

namespace TamkeenTech\LaravelEnumStateMachine\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use ReflectionClass;

use function Laravel\Prompts\error;
use function Laravel\Prompts\note;

class GenerateStatusFlowDiagram extends Command
{
    protected $signature = 'enum:flow-diagram {enum : The enum class name} {--output : Output directory for diagrams}';
    protected $description = 'Generate a status flow diagram from an enum';

    public function handle()
    {
        $enumClass = $this->argument('enum');
        $outputPath = $this->option('output') ?: config('enum-diagram.output_directory');

        if (!enum_exists($enumClass)) {
            error("Enum class {$enumClass} not found.");
            return 1;
        }

        // Get all enum cases and their transitions
        $statuses = $this->getStatusFlowData($enumClass);

        if (empty($statuses)) {
            error('You need to implement the transitions method');
            return 1;
        }

        // Extract enum name from full class path
        $enumName = (new ReflectionClass($enumClass))->getShortName();

        // Convert to JSON for Go script
        $jsonData = json_encode($statuses);

        // Get package root directory and construct path to Go script
        $packageRoot = dirname(__DIR__, 2);
        $goScript = $packageRoot . '/scripts/status_flow';

        // Execute Go script with JSON input and parameters
        $result = Process::run(sprintf(
            "echo '%s' | %s -path=%s -name=%s",
            $jsonData,
            $goScript,
            $outputPath,
            strtolower($enumName)
        ));

        if ($result->successful()) {
            // dd("Flow diagram generated successfully at {$outputPath}/{$enumName}.png");
            note("Flow diagram generated successfully at {$outputPath}/{$enumName}.png");
            return 0;
        }

        $this->error('Failed to generate diagram: ' . $result->errorOutput());
        return 1;
    }

    private function getStatusFlowData(string $enumClass): ?array
    {
        $statuses = [];

        foreach ($enumClass::cases() as $case) {
            // Check if transitions method exists
            if (!method_exists($case, 'transitions')) {
                return null;
            }

            $transitions = $case->transitions();

            // Convert enum cases to strings
            $nextStatuses = array_map(function ($transition) {
                return $transition->value;
            }, $transitions);

            $statuses[] = [
                'Name' => $case->value,
                'NextStatus' => $nextStatuses
            ];
        }

        return $statuses;
    }
}
