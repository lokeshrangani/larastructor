<?php

namespace LaraStructor\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'larastruct:make-service
    {model : The name of the model (e.g. Product)}
    {--model-path=Models : Relative path to the model directory (default: Models)}';

    protected $description = 'Generate a Service class';

    public function handle()
    {
        $model = Str::studly($this->argument('model'));
        $relativeModelPath = trim($this->option('model-path'), '/\\');
        $modelPath = app_path("{$relativeModelPath}/{$model}.php");

        // Check if the model exists
        if (!File::exists($modelPath)) {
            $this->error("❌ Model file not found at: {$modelPath}");
            $this->warn("Make sure the model exists or adjust the --model-path option.");
            return;
        }

        $modelNamespace = "App\\{$relativeModelPath}\\{$model}";

        // Prepare file names
        $serviceClass = "{$model}Service";
        $servicePath = app_path("Services/{$serviceClass}.php");

        // Ensure Services directory exists
        File::ensureDirectoryExists(app_path('Services'));

        // Create service class
        $serviceStub = File::get(__DIR__ . '/../../stubs/service.stub');
        $serviceContent = str_replace('{{ model }}', $model, $serviceStub);

        // Insert the model import dynamically
        $serviceContent = str_replace('{{ modelNamespace }}', $modelNamespace, $serviceContent);

        File::put($servicePath, $serviceContent);
        $this->info("✅ Service created at: App\\Services\\{$serviceClass}.php");

        $this->line("🎉 Done! Service setup complete for model: {$model}");
    }
}
