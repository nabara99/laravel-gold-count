<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class VerifiyIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role_id = auth()->user()->role_id;
        $adminId = Role::where('role_name', 'admin')->first()->id;
        if ($role_id !== $adminId) {
            return redirect('/home')->with('error', 'Anda tidak memiliki akses');
        }

        return $next($request);
    }
}
