<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalOrders = 0;
        $totalProducts = 0;
        $totalRevenue = 0;
        $recentOrders = collect();

        try {
            if (Schema::hasTable('admin_orders')) {
                $totalOrders = \App\Models\AdminOrder::count();
                $totalRevenue = \App\Models\AdminOrder::where('status', 'completed')->sum('total');
                $recentOrders = \App\Models\AdminOrder::latest()->take(5)->get();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        try {
            if (Schema::hasTable('admin_products')) {
                $totalProducts = \App\Models\AdminProduct::count();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
