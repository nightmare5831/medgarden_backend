<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\GoldPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $stats = [
            'total_users' => User::count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_professionals' => User::whereIn('role', ['professional', 'association', 'store'])->count(),
            'professionals_last_7_days' => User::whereIn('role', ['professional', 'association', 'store'])->where('created_at', '>=', $sevenDaysAgo)->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'approved_products' => Product::where('status', 'approved')->count(),
            'rejected_products' => Product::where('status', 'rejected')->count(),
        ];

        $recent_professionals = User::whereIn('role', ['professional', 'association', 'store'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_products = Product::with('seller')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get last 10 gold prices for chart (used for calculating change percentage in frontend)
        $goldPriceHistory = GoldPrice::getRecentForChart(10);

        $startDate = Carbon::now()->subDays(7);
        $professionalRegistrations = User::whereIn('role', ['professional', 'association', 'store'])
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $chartData = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartData[] = [
                'date' => $date,
                'count' => $professionalRegistrations->get($date)?->count ?? 0,
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_professionals' => $recent_professionals,
            'recent_products' => $recent_products,
            'gold_price_history' => $goldPriceHistory,
            'professional_registrations_chart' => $chartData,
        ]);
    }
}
