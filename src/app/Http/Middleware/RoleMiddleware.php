<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->user();

    if (!$user) {
      return redirect()->route('login');
    }

    $role = (int) $user->role;

    $isUnion = $role === 0;
    $isAdmin = in_array($role, [1, 2], true);

    if ($isUnion && $request->routeIs('admin.dashboard')) {
      return redirect()->route('union.dashboard');
    }

    if ($isAdmin && $request->routeIs('union.dashboard')) {
      return redirect()->route('admin.dashboard');
    }

    if (!$isUnion && !$isAdmin) {
      abort(403, 'Unauthorized role');
    }

    return $next($request);
  }
}