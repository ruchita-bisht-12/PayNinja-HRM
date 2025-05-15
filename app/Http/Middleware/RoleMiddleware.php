<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import the Log Facade

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log when middleware is triggered
        Log::info('RoleMiddleware triggered for request to: ' . $request->path());

        // Check if the user is authenticated
        if (!Auth::check()) {
            Log::warning('Unauthorized access attempt - user not authenticated');
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Log the user's email and role
        Log::info('Authenticated user: ' . $user->email . ' with role: ' . $user->role);

        // Check if the user has a required role (you might need to define $roles)
        $roles = ['superadmin', 'admin', 'user', 'employee']; // Define required roles

        if (!in_array($user->role, $roles)) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt by user: ' . $user->email . ' with role: ' . $user->role);

            // Redirect to unauthorized page
            return redirect()->route('unauthorized');
        }

        // Allow the request to continue
        return $next($request);
    }
}
