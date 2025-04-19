<?php

namespace LaraStructor\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'larastruct:make-repository 
                            {model : The name of the model (e.g. Product)} 
                            {--model-path=Models : Relative path to the model directory (default: Models)}';

    protected $description = 'Generate a repository class, optionally with an interface';

    public function handle()
    {
        $model = Str::studly($this->argument('model'));
        $relativeModelPath = trim($this->option('model-path'), '/\\');
        $modelPath = app_path("{$relativeModelPath}/{$model}.php");

        if (!File::exists($modelPath)) {
            $this->error("❌ Model file not found: {$relativeModelPath}/{$model}.php");
            $this->warn("Please create the model before generating its repository.");
            return;
        }

        // Get the full model class name (e.g., App\Models\Product)
        $modelNamespace = "App\\{$relativeModelPath}\\{$model}";
        $repositoryClass = "{$model}Repository";
        $interfaceName = "{$model}RepositoryInterface";

        $repositoryPath = app_path("Repositories/{$repositoryClass}.php");
        $interfacePath = app_path("Repositories/Interfaces/{$interfaceName}.php");

        // Ensure the directory exists
        File::ensureDirectoryExists(app_path('Repositories'));
        File::ensureDirectoryExists(app_path('Repositories/Interfaces'));

        // Repository stub
        $repositoryStub = File::get(__DIR__ . '/../../stubs/repository.stub');
        $repositoryContent = str_replace('{{ model }}', $model, $repositoryStub);
        $repositoryContent = str_replace('{{ modelNamespace }}', $modelNamespace, $repositoryContent);

        File::put($repositoryPath, $repositoryContent);
        $this->info("✅ Repository created at: Repositories/{$repositoryClass}.php");

        // Interface stub (optional)
        $interfaceStub = File::get(__DIR__ . '/../../stubs/repository-interface.stub');
        $interfaceContent = str_replace('{{ model }}', $model, $interfaceStub);
        $interfaceContent = str_replace('{{ modelNamespace }}', $modelNamespace, $interfaceContent);

        File::put($interfacePath, $interfaceContent);
        $this->info("✅ Interface created at: Repositories/Interface/{$interfaceName}.php");

        $this->line("🎉 Done! Happy coding, $model ninja!");
    }
}
