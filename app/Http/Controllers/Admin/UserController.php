<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $term = $request->string('q')->trim();
            $query->where(function ($qry) use ($term) {
                $qry->where('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%')
                    ->orWhere('phone', 'like', '%'.$term.'%');
            });
        }

        if ($request->role === 'admin') {
            $query->where('role', 'admin');
        } elseif ($request->role === 'customer') {
            $query->where('role', 'customer');
        }

        if ($request->active === '1') {
            $query->where('is_active', true);
        } elseif ($request->active === '0') {
            $query->where('is_active', false);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['posts', 'orders', 'addresses']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'role' => ['required', 'in:admin,customer'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (! $this->adminCountAfterRoleChange($user, $data['role'])) {
            return back()
                ->withInput()
                ->with('error', 'Phải có ít nhất một tài khoản quản trị trong hệ thống.');
        }

        if ($user->isAdmin() && ! $data['is_active'] && ! $this->hasOtherActiveAdmin($user)) {
            return back()
                ->withInput()
                ->with('error', 'Không thể vô hiệu hóa admin cuối cùng đang hoạt động.');
        }

        if (! $request->filled('password')) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && ! Str::startsWith($user->avatar, ['http://', 'https://'])) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Đã cập nhật tài khoản «'.$user->name.'».');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Bạn không thể xóa chính tài khoản đang đăng nhập.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->where('id', '!=', $user->id)->count() === 0) {
            return back()->with('error', 'Không thể xóa admin cuối cùng.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Đã chuyển tài khoản vào thùng rác.');
    }

    public function trashed(Request $request)
    {
        $query = User::onlyTrashed();

        if ($request->filled('q')) {
            $search = $request->string('q')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $users = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('admin.users.trashed', compact('users'));
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        $user->restore();

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'Đã khôi phục tài khoản «'.$user->name.'».');
    }

    public function forceDestroy(Request $request, int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Thao tác không hợp lệ.');
        }

        if ($user->posts()->exists()) {
            return back()->with('error', 'Không thể xóa vĩnh viễn: tài khoản còn bài viết (tác giả).');
        }

        if ($user->orders()->exists()) {
            return back()->with('error', 'Không thể xóa vĩnh viễn: tài khoản còn đơn hàng.');
        }

        if (Order::where('confirmed_by', $user->id)->exists()) {
            return back()->with('error', 'Không thể xóa vĩnh viễn: tài khoản liên kết đơn hàng đã xác nhận.');
        }

        if ($user->orderStatusHistories()->exists()) {
            return back()->with('error', 'Không thể xóa vĩnh viễn: tài khoản còn lịch sử cập nhật đơn hàng.');
        }

        if ($user->avatar && ! Str::startsWith($user->avatar, ['http://', 'https://'])) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->forceDelete();

        return redirect()
            ->route('admin.users.trashed')
            ->with('success', 'Đã xóa vĩnh viễn tài khoản «'.$user->email.'».');
    }

    /**
     * Sau khi đổi role, vẫn phải còn ít nhất 1 admin.
     */
    private function adminCountAfterRoleChange(User $user, string $newRole): bool
    {
        $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
        $willBeAdmin = $newRole === 'admin' ? 1 : 0;

        return ($otherAdmins + $willBeAdmin) >= 1;
    }

    private function hasOtherActiveAdmin(User $user): bool
    {
        return User::where('role', 'admin')
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->exists();
    }
}
