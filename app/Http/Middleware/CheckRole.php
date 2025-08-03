<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();
        
        switch ($role) {
            case 'admin_general':
                if (!$user->isAdminGeneral()) {
                    abort(403, 'Accès réservé aux administrateurs généraux');
                }
                break;
            case 'owner_os':
                if (!$user->isOwnerOS() && !$user->isAdminGeneral()) {
                    abort(403, 'Accès réservé aux owners d\'objectifs stratégiques');
                }
                break;
            case 'owner_pil':
                if (!$user->isOwnerPIL() && !$user->isOwnerOS() && !$user->isAdminGeneral()) {
                    abort(403, 'Accès réservé aux owners de pilotage');
                }
                break;
            case 'owner_action':
                if (!$user->isOwnerAction() && !$user->isOwnerPIL() && !$user->isOwnerOS() && !$user->isAdminGeneral()) {
                    abort(403, 'Accès réservé aux owners d\'actions');
                }
                break;
            case 'owner_sa':
                if (!$user->isOwnerSA() && !$user->isOwnerAction() && !$user->isOwnerPIL() && !$user->isOwnerOS() && !$user->isAdminGeneral()) {
                    abort(403, 'Accès réservé aux owners de sous-actions');
                }
                break;
            default:
                abort(403, 'Rôle non reconnu');
        }

        return $next($request);
    }
}
