<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $requiredRole): Response
    {
        $user = Session::get('user');

        if (!$user || !isset($user['role'])) {
            return redirect()->route('login');
        }

        $userRole = $user['role'];

        // Admin has access to everything
        if ($userRole === 'admin') {
            return $next($request);
        }

        // For other roles, check exact match
        if ($userRole !== $requiredRole) {
            return match ($userRole) {
                'admin' => redirect()->route('admin.dashboard'),
                'responsable' => redirect()->route('responsable.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
                default => redirect()->route('login'),
            };
        }

        return $next($request);
    }
}