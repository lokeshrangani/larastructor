<?php

namespace LaraStructor\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepositoryServiceCommand extends Command
{
    protected $signature = 'larastruct:make-repository-service
    {repository : The name of the model (e.g. Product)}
    {--repository-path=Repositories : Relative path to the repositories directory (default: Repositories)}';

    protected $description = 'Generate a Service class for a repository';

    public function handle()
    {
        $repository = Str::studly($this->argument('repository'));
        $relativeRepositoryPath = trim($this->option('repository-path'), '/\\');
        $repositoryClassPath = app_path("{$relativeRepositoryPath}/{$repository}Repository.php");
        $repositoryInterfacePath = app_path("{$relativeRepositoryPath}/Interfaces/{$repository}RepositoryInterface.php");

        // ✅ Check if repository file exists
        if (!File::exists($repositoryClassPath)) {
            $this->error("❌ Repository class not found at: {$repositoryClassPath}");
            return;
        }

        // ✅ Check if repository interface exists
        if (!File::exists($repositoryInterfacePath)) {
            $this->error("❌ Repository interface not found at: {$repositoryInterfacePath}");
            return;
        }

        // ✅ Namespace generation
        $repositoryNamespace = "App\\{$this->normalizeSlashes($relativeRepositoryPath)}\\{$repository}Repository";
        $interfaceNamespace = "App\\{$this->normalizeSlashes($relativeRepositoryPath)}\\Interfaces\\{$repository}RepositoryInterface";

        // ✅ Generate service file
        $serviceClass = "{$repository}Service";
        $servicePath = app_path("Services/{$serviceClass}.php");

        File::ensureDirectoryExists(app_path('Services'));

        $stubPath = __DIR__ . '/../../stubs/service-with-repository.stub';

        if (!File::exists($stubPath)) {
            $this->error("❌ Stub file not found at: {$stubPath}");
            return;
        }

        $serviceContent = File::get($stubPath);
        $serviceContent = str_replace(
            ['{{ repository }}', '{{ repositoryNamespace }}', '{{ interfaceNamespace }}'],
            [$repository, $repositoryNamespace, $interfaceNamespace],
            $serviceContent
        );

        File::put($servicePath, $serviceContent);

        $this->info("✅ Service class created at: App\\Services\\{$serviceClass}.php");
        $this->line("🎉 Done! Service setup complete for repository: {$repository}");
    }

    protected function normalizeSlashes(string $path): string
    {
        return str_replace('/', '\\', $path);
    }
}
