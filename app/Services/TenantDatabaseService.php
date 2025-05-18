<?php
namespace App\Services;
use Illuminate\Support\Facades\{Artisan, DB, Config};
class TenantDatabaseService {
    private $tenant, $domain, $database, $username, $password;
    public function createDB($tenant) {
        DB::statement("CREATE DATABASE " . $tenant->database);
    }

    public function connectToDB($tenant) {
        DB::purge('system');
        DB::purge('tenant');
        Config::set('database.connections.tenant.database', $tenant->database);
        $this->tenant = $tenant;
        $this->domain = $tenant->domain;
        $this->database = $tenant->database;
        $this->username = $tenant->username;
        $this->password = $tenant->password;
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');
    }

    public function migrateToDB($tenant) {
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenants'
        ]);
    }
}
