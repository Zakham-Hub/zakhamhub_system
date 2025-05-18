<?php
namespace App\Console\Commands\Tenants;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Artisan};
use App\Services\TenantDatabaseService;
class TenantMigrateCommand extends Command {
    protected $signature = 'tenant:migrate';
    protected $description = 'Migrate tenant database';
    protected $migration_path = 'database/migrations/tenants';
    protected $migrate_action = 'migrate';
    protected $database_conn = 'tenant';
    protected $migration_options;
    public function handle() {
        $this->info('Migrating tenant database...');
        $tenants = DB::table('tenants')->get();
        $tenantService = new TenantDatabaseService();
        $tenants->each(function ($tenant) use ($tenantService) {
            $tenantService->connectToDB($tenant);
            $this->info('Migrating tenant: ' . $tenant?->domain . ' ' . $tenant?->subdomain . ' - ' . $tenant->name . ' - database name : ' . $tenant->database);
            $this->info('...........');
            $this->migration_options = $this->ask('Enter the migration options if you want to rollback or reset the migration. Leave blank if you want to only migrate');
            if ($this->migration_options == null) {
                Artisan::call($this->migrate_action, [
                    '--path' => $this->migration_path,
                    '--database' => $this->database_conn
                ]);
                $this->info(Artisan::output());
            } else {
                Artisan::call($this->migrate_action . ':' . $this->migration_options, [
                    '--path' => $this->migration_path,
                    '--database' => $this->database_conn,
                ]);
                $this->info(Artisan::output() . $tenant?->domain . ' ' . $tenant?->subdomain . ' successfully ' . $this->migration_options);
            }
        });
        return Command::SUCCESS;
    }
}
