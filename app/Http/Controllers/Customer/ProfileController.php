<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->latest(),
        ]);

        $primaryAddress = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();

        return view('customers.profile.index', [
            'user' => $user,
            'primaryAddress' => $primaryAddress,
            'addresses' => $user->addresses,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

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
            ->route('profile')
            ->with('success', 'Đã cập nhật thông tin cá nhân.');
    }

    public function storeAddress(Request $request)
    {
        $user = $request->user();
        $data = $this->validateAddressData($request, 'addressStore');

        DB::transaction(function () use ($user, $request, $data) {
            $isDefault = $request->boolean('is_default') || ! $user->addresses()->exists();

            if ($isDefault) {
                $this->clearDefaultAddresses($user->id);
            }

            $user->addresses()->create([
                ...$data,
                'is_default' => $isDefault,
            ]);
        });

        return redirect()
            ->route('profile')
            ->withFragment('addresses')
            ->with('success', 'Đã thêm địa chỉ nhận hàng mới.');
    }

    public function updateAddress(Request $request, Address $address)
    {
        $this->authorizeOwnedAddress($request->user()->id, $address);

        $data = $this->validateAddressData($request, 'addressUpdate');

        DB::transaction(function () use ($request, $address, $data) {
            $isDefault = $request->boolean('is_default') || $address->is_default;

            if ($request->boolean('is_default')) {
                $this->clearDefaultAddresses($address->user_id);
                $isDefault = true;
            }

            $address->update([
                ...$data,
                'is_default' => $isDefault,
            ]);
        });

        return redirect()
            ->route('profile')
            ->withFragment('addresses')
            ->with('success', 'Đã cập nhật địa chỉ nhận hàng.');
    }

    public function setDefaultAddress(Request $request, Address $address)
    {
        $this->authorizeOwnedAddress($request->user()->id, $address);

        DB::transaction(function () use ($address) {
            $this->clearDefaultAddresses($address->user_id);
            $address->update(['is_default' => true]);
        });

        return redirect()
            ->route('profile')
            ->withFragment('addresses')
            ->with('success', 'Đã đặt địa chỉ mặc định.');
    }

    public function destroyAddress(Request $request, Address $address)
    {
        $this->authorizeOwnedAddress($request->user()->id, $address);

        $userId = $address->user_id;
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($address, $userId, $wasDefault) {
            $address->delete();

            if ($wasDefault) {
                $nextAddress = Address::where('user_id', $userId)->latest()->first();

                if ($nextAddress) {
                    $nextAddress->update(['is_default' => true]);
                }
            }
        });

        return redirect()
            ->route('profile')
            ->withFragment('addresses')
            ->with('success', 'Đã xóa địa chỉ nhận hàng.');
    }

    private function validateAddressData(Request $request, string $bag): array
    {
        return $request->validateWithBag($bag, [
            'receiver_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'province' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'address_detail' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
        ]);
    }

    private function clearDefaultAddresses(int $userId): void
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);
    }

    private function authorizeOwnedAddress(int $userId, Address $address): void
    {
        if ((int) $address->user_id !== $userId) {
            abort(403, 'Unauthorized');
        }
    }
}
