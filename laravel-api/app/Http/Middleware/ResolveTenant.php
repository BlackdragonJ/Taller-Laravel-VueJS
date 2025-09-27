<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class ResolveTenant{

    public function handle(Request $request, Closure $next){

        $host = $request->getHost();
        $sub = explode(',', $host)[0] ?? null;

        if(!$sub || in_array($sub, ['www'.'api','localhost'])){
            return $next($request);
        }

        $t = Tenant::where('subdomain', $sub)->first();
        if (!$t) abort(404, 'Tenant not found');

        Config::set('database.connections.tenant', array_merge(
            config('database.connections.tenant'),
            [
                'host'     => $t->db_host,
                'database' => $t->db_name,
                'username' => $t->db_user,
                'password' => $t->db_password,
            ]
        ));
        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        return $next($request);
    }
}
