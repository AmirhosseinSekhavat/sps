<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		// Allow Filament admin login route to be accessed
		if ($request->is('admin/login') || $request->is('admin/login/*')) {
			return $next($request);
		}

		if (!Auth::check()) {
			// Redirect unauthenticated users to Filament admin login
			return redirect('/admin/login');
		}

		$user = Auth::user();
		
		// Check if user is admin
		if (!$user->is_admin) {
			abort(403, 'دسترسی غیرمجاز');
		}

		return $next($request);
	}
}
