<?php
namespace App\Console\Commands\Tenants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
class CreateTenantMigrationCommand extends Command {
    protected $signature = 'tenants:make-migration {name : The name of the migration}';
    protected $description = 'Create a new migration file inside database/migrations/tenants';
    public function handle() {
        $name = $this->argument('name');
        $path = database_path('migrations/tenants');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        Artisan::call('make:migration', [
            'name' => $name,
            '--path' => 'database/migrations/tenants',
        ]);
        $this->info("Migration created in: database/migrations/tenants");
    }
}
