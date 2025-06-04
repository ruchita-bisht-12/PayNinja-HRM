<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ModuleAccess;

class LoadModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only load module access settings if user has an employee record with a company
            if ($user->employee && $user->employee->company) {
                $company = $user->employee->company;
                
                try {
                    // Get current module access settings
                    $modules = ModuleAccess::where('company_id', $company->id)
                        ->get()
                        ->groupBy('module_name')
                        ->map(function ($moduleGroup) {
                            return $moduleGroup->mapWithKeys(function ($access) {
                                // Explicitly cast to boolean to ensure proper type
                                return [$access->role => (bool)$access->has_access];
                            });
                        })
                        ->toArray();
                        
                    // Ensure company_admin role always has access to all modules
                    foreach ($modules as $moduleName => &$roleAccess) {
                        // Company admin always has full access
                        $roleAccess['company_admin'] = true;
                        
                        // Admin has access by default, but can be restricted
                        if (!isset($roleAccess['admin'])) {
                            $roleAccess['admin'] = true; // Default to true for admin if not set
                        }
                        
                        // Ensure other roles have proper access settings
                        $allowedRoles = ['employee', 'reporter', 'reportee'];
                        foreach ($allowedRoles as $role) {
                            if (!isset($roleAccess[$role])) {
                                $roleAccess[$role] = true; // Default to true if not set
                            }
                        }
                    }
                    
                    // Store modules in session for sidebar access
                    session(['modules' => $modules]);
                    
                    // Force session to save immediately
                    session()->save();
                    
                    // Log for debugging
                    Log::debug('Module access loaded in middleware', [
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'modules' => $modules
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error loading module access in middleware: ' . $e->getMessage(), [
                        'user_id' => $user->id,
                        'company_id' => $company->id
                    ]);
                }
            }
        }
        
        return $next($request);
    }
}
