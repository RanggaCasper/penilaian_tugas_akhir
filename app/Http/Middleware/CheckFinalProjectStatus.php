<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FinalProject\FinalProject;
use Symfony\Component\HttpFoundation\Response;

class CheckFinalProjectStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $finalProject = FinalProject::with('period')
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->first();

        if (!$finalProject) {
            abort(403);
        }
        
        return $next($request);
    }
}
