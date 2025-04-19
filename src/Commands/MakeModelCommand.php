<?php

namespace LaraStructor\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModelCommand extends Command
{
    protected $signature = 'larastruct:make-model';
    protected $description = 'Interactive model creator for new Laravel developers';

    public function handle()
    {
        $name = '';
        while (empty($name)) {
            $name = Str::studly($this->ask('Enter the model name (e.g. Product)'));
        }
        $defaultTable = Str::snake(Str::pluralStudly($name));
        $table = $this->ask("Enter table name (leave blank to use default: {$defaultTable})") ?: $defaultTable;

        $fillable = '';
        while (empty($fillable)) {
            $fillable = $this->ask('Enter fillable fields (comma separated)');
        }
        $hidden = $this->ask('Enter hidden fields (comma separated, optional)', '');
        $softDeletes = $this->confirm('Should the model support soft deletes?', false);

        $this->info("\n🔍 Please confirm your inputs:");
        $this->line("Model Name   : $name");
        $this->line("Table Name   : $table");
        $this->line("Fillable     : $fillable");
        $this->line("Hidden       : $hidden");
        $this->line("Soft Deletes : " . ($softDeletes ? 'Yes' : 'No'));

        if (! $this->confirm("\n✅ Do you want to continue with this?", true)) {
            $this->warn("❌ Aborted by user.");
            return;
        }

        $this->info("🚀 Generating model...");

        $stubPath = __DIR__ . '/../../stubs/model.stub';
        if (!File::exists($stubPath)) {
            $this->error("Stub file not found at: $stubPath");
            return;
        }

        $stub = File::get($stubPath);
        $modelContent = str_replace('{{ class }}', $name, $stub);

        $modelContent = str_replace(
            '// table',
            "protected \$table = '$table';",
            $modelContent
        );

        $fillableArray = collect(explode(',', $fillable))
            ->map(fn($f) => "'" . trim($f) . "'")
            ->filter()
            ->values()
            ->toArray();

        $fillableString = 'protected $fillable = [' . implode(', ', $fillableArray) . '];';
        $modelContent = str_replace('// fillable', $fillableString, $modelContent);

        if ($hidden) {
            $hiddenArray = collect(explode(',', $hidden))
                ->map(fn($f) => "'" . trim($f) . "'")
                ->filter()
                ->values()
                ->toArray();

            $hiddenString = 'protected $hidden = [' . implode(', ', $hiddenArray) . '];';
            $modelContent = str_replace('// hidden', $hiddenString, $modelContent);
        } else {
            $modelContent = str_replace('// hidden', '', $modelContent);
        }

        if ($softDeletes) {
            $modelContent = str_replace('use HasFactory;', 'use HasFactory, SoftDeletes;', $modelContent);
            $modelContent = str_replace('// softdelete', 'use Illuminate\\Database\\Eloquent\\SoftDeletes;', $modelContent);
        } else {
            $modelContent = str_replace('// softdelete', '', $modelContent);
        }

        $modelPath = app_path("Models/{$name}.php");
        if (! File::exists(app_path('Models'))) {
            File::makeDirectory(app_path('Models'), 0755, true);
        }

        File::put($modelPath, $modelContent);

        $this->info("✅ Model $name created successfully:");
        $this->line("- Location: app/Models/$name.php");
        $this->line("- Table: $table");
        $this->line("- Fillable: " . implode(', ', $fillableArray));
        if ($hidden) $this->line("- Hidden: " . implode(', ', $hiddenArray));
        if ($softDeletes) $this->line("- Soft Deletes: enabled");
    }
}
