<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOrder;
use App\Models\AdminProduct;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalOrders = AdminOrder::count();
        $totalProducts = AdminProduct::count();
        $totalRevenue = AdminOrder::where('status', 'completed')->sum('total');
        $recentOrders = AdminOrder::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
