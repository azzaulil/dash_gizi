<?php

namespace App\Http\Middleware;

use Closure;

class ActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->is_active == 0)
        {
            return response()->json(['message' => 'Akun anda belum aktif, silahkan cek email anda untuk mengaktifkan'], 401);
        }
        return $next($request);
    }
}
