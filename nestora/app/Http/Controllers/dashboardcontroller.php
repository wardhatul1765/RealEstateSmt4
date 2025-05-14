<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Dummy data atau ambil dari database
        $totalProperties = 100;
        $totalViews = 3000;
        $soldThisMonth = 85;
        $activeAgents = 3;

        // Bar chart: Penjualan per bulan
        $salesPerMonth = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
            'data' => [10, 20, 30, 25, 40]
        ];

        // Line chart: Tren pengunjung
        $visitors = [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'data' => [500, 800, 600, 900]
        ];

        // Pie chart: Distribusi properti terjual berdasarkan lokasi
        $soldDistribution = [
            'labels' => ['Business Bay', 'JBR', 'Dubai Land', 'The Springs'],
            'data' => [20, 25, 30, 10]
        ];

        return view('dashboard', compact(
            'totalProperties',
            'totalViews',
            'soldThisMonth',
            'activeAgents',
            'salesPerMonth',
            'visitors',
            'soldDistribution'
        ));
    }
}
?>
