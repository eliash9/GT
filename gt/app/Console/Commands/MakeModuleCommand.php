<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DDD module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $basePath = base_path('src/Modules/' . $name);

        if (File::exists($basePath)) {
            $this->error("Module {$name} already exists!");
            return;
        }

        $dirs = [
            'Application/DTOs',
            'Application/UseCases',
            'Domain/Entities',
            'Domain/ValueObjects',
            'Domain/Repositories',
            'Infrastructure/Repositories',
            'Infrastructure/Models',
            'Presentation/Controllers',
            'Presentation/Requests',
            'Presentation/Resources',
        ];

        foreach ($dirs as $dir) {
            $path = "{$basePath}/{$dir}";
            File::makeDirectory($path, 0755, true);
        }

        $this->createController($name, $basePath);
        $this->createModel($name, $basePath);
        $this->info("Module {$name} created successfully at {$basePath}");
    }

    protected function createController($name, $basePath)
    {
        $stub = <<<EOT
<?php

namespace Modules\\{$name}\\Presentation\\Controllers;

use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use Inertia\\Inertia;
use Inertia\\Response;

class {$name}Controller extends Controller
{
    public function index(): Response
    {
        return Inertia::render('{$name}/Index');
    }
}
EOT;
        File::put("{$basePath}/Presentation/Controllers/{$name}Controller.php", $stub);
    }

    protected function createModel($name, $basePath)
    {
        $stub = <<<EOT
<?php

namespace Modules\\{$name}\\Infrastructure\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;

class {$name} extends Model
{
    use HasFactory;

    protected \$guarded = [];
}
EOT;
        File::put("{$basePath}/Infrastructure/Models/{$name}.php", $stub);
    }
}
