<?php

namespace App\Middleware;

use App\Database\Pool;

class PoolManager
{


    public static function handle($request, $response, $next)
    {

        $pdo = Pool::get();
        try {
            return $next();
        } finally {
            Pool::put($pdo);
        }
    }
};
