<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {

    if (! Auth::check()) {
      return redirect()->route('login');
    }

    // Check if email_verified_at is not null
    $user = Auth::user();
    if (is_null($user->email_verified_at)) {
      Session::put('verification_required_for_user', $user->id);

      return redirect()->route('verification');
    }

    return $next($request);
  }
}
