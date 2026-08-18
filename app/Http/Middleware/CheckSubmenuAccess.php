<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubmenuAccess
{
    /**
     * Contoh pemakaian di route: ->middleware('submenu:pasien')
     */
    public function handle(Request $request, Closure $next, string $submenu): Response
    {
        $user = $request->user();

        if (! $user || ! $user->bisaAksesSubmenu($submenu)) {
            abort(403, 'Anda tidak punya akses ke sub-menu ini. Hubungi Manajer divisi Anda.');
        }

        return $next($request);
    }
}