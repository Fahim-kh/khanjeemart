<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @param  string|null  $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $module, string $action = null)
    {
        // Get the authenticated user
        $user = $request->user();
        
        // If no user is authenticated, deny access
        if (!$user) {
            abort(403, 'Unauthorized - Please login');
        }

        // Get all permissions for the module
        $permissions = $user->hasPer($module);
        
        // If module doesn't exist or no permissions returned
        if (!$permissions) {
            abort(403, "Unauthorized - No permissions for {$module}");
        }

        // If specific action requested
        if ($action) {
            $validActions = ['pview', 'pcreate', 'pedit', 'pdelete'];
            
            // Validate action parameter
            if (!in_array($action, $validActions)) {
                abort(400, "Invalid permission action: {$action}");
            }
            
            // Check specific permission
            if (empty($permissions[$action])) {
                $alissAction = '';
                if($action == 'pview'){
                    $alissAction = 'View';
                } elseif($action == 'pcreate'){
                    $alissAction = 'Create';
                } elseif($action == 'pedit'){
                    $alissAction = 'Edit';
                } elseif($action == 'delete'){
                    $alissAction = 'Delete';
                }
                
                abort(403, "Unauthorized - Missing {$alissAction} permission for {$module}");
            }
            
            return $next($request);
        }

        // If no specific action, check if user has any permissions
        if (!array_filter($permissions)) {
            abort(403, "Unauthorized - No permissions granted for {$module}");
        }

        return $next($request);
    }
}