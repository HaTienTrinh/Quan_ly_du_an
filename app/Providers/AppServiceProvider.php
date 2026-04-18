<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('customers.layouts.layout', function ($view) {
            $myContacts = auth()->check()
                ? \App\Models\Contact::where('user_id', auth()->id())
                    ->latest()
                    ->take(10)
                    ->get()
                : collect();

            $view->with('myContacts', $myContacts);
        });
    }
}
