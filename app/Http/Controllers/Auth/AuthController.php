<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ============ ĐĂNG KÝ ============

    // US1: Hiển thị form đăng ký
    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.register');
    }

    // US1: Xử lý đăng ký
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password, // Model tự hash qua cast
            'phone'    => $request->phone,
            'role'     => 'customer',
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn.');
    }

    // ============ ĐĂNG NHẬP ============

    // US2, US3: Hiển thị form đăng nhập
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }
        return view('auth.login');
    }

    // US2, US3: Xử lý đăng nhập
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email hoặc mật khẩu không đúng.');
        }

        $user = Auth::user();

        // Kiểm tra tài khoản có bị vô hiệu hóa không
        if (!$user->is_active) {
            Auth::logout();
            return back()->with('error', 'Tài khoản của bạn đã bị vô hiệu hóa.');
        }

        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    // ============ ĐĂNG XUẤT ============

    // US4: Đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đăng xuất thành công.');
    }

    // ============ HELPER ============

    // Phân quyền chuyển hướng sau đăng nhập
    private function redirectByRole()
    {
        /** @var User $user */
        $user = Auth::user();
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
}
