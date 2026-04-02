<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('defaultAddress', 'addresses');

        $primaryAddress = $user->defaultAddress ?? $user->addresses->sortByDesc('created_at')->first();

        return view('customers.profile.index', [
            'user' => $user,
            'primaryAddress' => $primaryAddress,
        ]);
    }
}
