<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTaskCreationPermission
{

    public function handle($request, Closure $next)
{
    // added permission
    if (auth()->user()->role === 'admin' || auth()->user()->can_create_tasks != 0) {
        return $next($request);
    }

    return redirect()->back()->with('error', 'You do not have permission to create tasks.');
}
}
