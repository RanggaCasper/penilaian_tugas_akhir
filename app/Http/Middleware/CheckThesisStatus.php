<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Thesis\Thesis;
use Symfony\Component\HttpFoundation\Response;

class CheckThesisStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $Thesis = Thesis::with('period')
            ->where('student_id', Auth::id())
            ->where('status', 'disetujui')
            ->first();

        if (!$Thesis) {
            abort(403);
        }
        
        return $next($request);
    }
}
