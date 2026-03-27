<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        /** @var User $user */
        $user = Auth::user();
        
        if (!$user->isCustomer()) {
            return redirect()->route('admin.dashboard');
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tài khoản đã bị vô hiệu hóa.');
        }

        return $next($request);
    }
}
