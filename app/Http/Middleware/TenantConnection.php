<?php

namespace App\Http\Middleware;

use App\Services\TenantDatabaseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
class TenantConnection {
    public function handle(Request $request, Closure $next): Response {
        $host = $request->getHost();
        $mainDomain = env('MAIN_APP_URL_DOMAIN');
        if ($host !== $mainDomain) {
            $subdomain = str_replace('.' . $mainDomain, '', $host);
            $tenant = DB::table('tenants')
                ->where('domain', $host)
                ->orWhere('subdomain', $host)
                ->first();
            if($tenant && config('database.connections.tenant.database') != $tenant->database) {
                (new TenantDatabaseService())->connectToDB($tenant);
                (new TenantDatabaseService())->migrateToDB($tenant);
            } else {
                dd('Tenant not found for domain: ' . $host);
            }
        }


        return $next($request);
    }
}
