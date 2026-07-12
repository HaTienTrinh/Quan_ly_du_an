<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::where('role', 'customer')->count(),
            'total_products'   => Product::count(),
            'total_categories' => Category::count(),
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::byStatus('pending')->count(),
            'revenue'          => Order::where('status', 'delivered')->sum('total_amount'),
            'unread_contacts'  => Contact::where('status', 'unread')->count(),
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();

        // Doanh thu theo tuần (7 ngày gần nhất)
        $weeklyRevenue = $this->getRevenueByPeriod('week');

        // Doanh thu theo tháng (12 tháng gần nhất)
        $monthlyRevenue = $this->getRevenueByPeriod('month');

        // Doanh thu theo năm (5 năm gần nhất)
        $yearlyRevenue = $this->getRevenueByPeriod('year');

        return view('admin.dashboard', compact(
            'stats', 'recent_orders',
            'weeklyRevenue', 'monthlyRevenue', 'yearlyRevenue'
        ));
    }

    private function getRevenueByPeriod(string $period): array
    {
        $labels = [];
        $data   = [];

        if ($period === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date     = Carbon::now()->subDays($i);
                $labels[] = $date->format('d/m');
                $data[]   = (float) Order::where('status', 'delivered')
                    ->whereDate('updated_at', $date->toDateString())
                    ->sum('total_amount');
            }
        } elseif ($period === 'month') {
            for ($i = 11; $i >= 0; $i--) {
                $date     = Carbon::now()->subMonths($i);
                $labels[] = 'T' . $date->format('n/Y');
                $data[]   = (float) Order::where('status', 'delivered')
                    ->whereYear('updated_at', $date->year)
                    ->whereMonth('updated_at', $date->month)
                    ->sum('total_amount');
            }
        } elseif ($period === 'year') {
            for ($i = 4; $i >= 0; $i--) {
                $year     = Carbon::now()->subYears($i)->year;
                $labels[] = (string) $year;
                $data[]   = (float) Order::where('status', 'delivered')
                    ->whereYear('updated_at', $year)
                    ->sum('total_amount');
            }
        }

        return compact('labels', 'data');
    }
}
