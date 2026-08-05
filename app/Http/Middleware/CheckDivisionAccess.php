<?php

namespace App\Http\Middleware;

use App\Models\Division;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDivisionAccess
{
    /**
     * Dipasang di route yang punya parameter {division} (route model binding by slug).
     * Contoh: Route::get('/divisi/{division:slug}', ...)->middleware('division.access')
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $division = $request->route('division');

        if (! $division instanceof Division) {
            abort(404);
        }

        if (! $user || ! $user->canAccessDivision($division->slug)) {
            abort(403, 'Anda tidak punya akses ke dashboard divisi ini.');
        }

        return $next($request);
    }
}
