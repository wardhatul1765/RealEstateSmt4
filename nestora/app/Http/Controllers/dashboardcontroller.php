<?php

namespace App\Http\Controllers;

use App\Models\UserProperty;
use Illuminate\Http\Request; // Keep for potential future use, though not directly used in this method
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime as MongoUTCDateTime; // This might be unused if not directly interacting with MongoDateTime objects for comparison/creation elsewhere.

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Harga rata-rata properti (keseluruhan)
            $averagePrice = UserProperty::avg('price');
            $averagePriceFormatted = 'AED ' . number_format($averagePrice ?? 0, 0, ',', '.');

            // Luas rata-rata properti (m² ke sqft) (keseluruhan)
            $averageSizeM2 = UserProperty::avg('sizeMin');
            $averageSizeSqft = $averageSizeM2 ? $averageSizeM2 * 10.7639 : 0;

            // Distribusi harga (keseluruhan)
            $priceDistributionRaw = UserProperty::select('price')->whereNotNull('price')->get();
            $priceDistribution = [];
            foreach ($priceDistributionRaw as $item) {
                // Group prices into ranges (e.g., 0-1M, 1M-2M, etc.)
                $range = floor($item->price / 1000000) * 1000000;
                $priceDistribution[$range] = ($priceDistribution[$range] ?? 0) + 1;
            }
            ksort($priceDistribution); // Sort by price range

            // Distribusi furnishing (keseluruhan)
            $furnishingDistributionRaw = UserProperty::raw(function ($collection) {
                return $collection->aggregate([
                    ['$match' => ['furnishing' => ['$ne' => null]]], // Filter out null furnishing values
                    ['$group' => [
                        '_id' => '$furnishing', // Group by furnishing type
                        'count' => ['$sum' => 1] // Count occurrences
                    ]],
                    ['$sort' => ['count' => -1]] // Sort by count descending
                ]);
            });

            $furnishingDistribution = ['labels' => [], 'data' => []];
            foreach ($furnishingDistributionRaw as $item) {
                $furnishingDistribution['labels'][] = $item->_id ?? 'Unknown'; // Use 'Unknown' for null IDs
                $furnishingDistribution['data'][] = $item->count;
            }

            // --- Filter data dari tahun 2024 hingga terbaru untuk grafik tren ---
            // Get properties created from January 1, 2024, up to the current date
            $propertiesForTrends = UserProperty::whereNotNull('created_at')
                ->whereBetween('created_at', [
                    Carbon::create(2024, 1, 1)->startOfDay(), // Start of January 1, 2024
                    Carbon::now()->endOfDay()                 // End is current day today
                ])
                ->get();

            // Properti ditambahkan per bulan (diurutkan), periode: 2024 - terbaru
            // Group properties by month of creation and sort by month
            $addedPropertiesGrouped = $propertiesForTrends->groupBy(function ($item) {
                // Ensure created_at is parsed as Carbon instance if it's not already
                $createdAt = $item->created_at instanceof Carbon ? $item->created_at : Carbon::parse($item->created_at);
                return $createdAt->format('Y-m'); // Format as 'Year-Month'
            })->sortKeys(); // Sort by the 'Year-Month' key

            $addedPropertiesPerMonth = ['labels' => [], 'data' => []];
            foreach ($addedPropertiesGrouped as $month => $items) {
                $monthName = Carbon::parse($month . '-01')->format('M Y'); // Format as 'Mon Year' (e.g., Jan 2024)
                $addedPropertiesPerMonth['labels'][] = $monthName;
                $addedPropertiesPerMonth['data'][] = count($items);
            }

            // Harga rata-rata properti per bulan (diurutkan), periode: 2024 - terbaru
            // Filter out properties with no price, group by month, and sort
            $avgPriceGrouped = $propertiesForTrends
                ->filter(fn($item) => $item->price > 0) // Ensure price is positive
                ->groupBy(function ($item) {
                    // Ensure created_at is parsed as Carbon instance
                    $createdAt = $item->created_at instanceof Carbon ? $item->created_at : Carbon::parse($item->created_at);
                    return $createdAt->format('Y-m'); // Format as 'Year-Month'
                })->sortKeys(); // Sort by the 'Year-Month' key

            $averagePricePerMonth = ['labels' => [], 'data' => []];
            foreach ($avgPriceGrouped as $month => $items) {
                $monthName = Carbon::parse($month . '-01')->format('M Y'); // Format as 'Mon Year'
                $averagePricePerMonth['labels'][] = $monthName;
                $averagePricePerMonth['data'][] = round($items->avg('price'), 0); // Calculate average price and round
            }

            // Return data to the view
            return view('dashboard', [
                'totalProperties' => UserProperty::count(),
                'averagePrice' => $averagePrice, // Raw average price
                'averagePriceFormatted' => $averagePriceFormatted, // Formatted average price
                'averageSize' => round($averageSizeSqft, 2), // Average size in sqft, rounded
                'propertiBelumTerverifikasi' => UserProperty::where('status', 'pendingVerification')->count(),
                'priceDistribution' => [
                    // Format price range labels (e.g., AED 0M, AED 1M)
                    'labels' => array_map(fn($range) => 'AED ' . number_format($range / 1000000, 0) . 'M', array_keys($priceDistribution)),
                    'data' => array_values($priceDistribution)
                ],
                'furnishingDistribution' => $furnishingDistribution,
                // 'sizePriceData' => $sizePriceData, // Removed as per previous request
                'addedPropertiesPerMonth' => $addedPropertiesPerMonth, // Data for 2024 - latest
                'averagePricePerMonth' => $averagePricePerMonth,   // Data for 2024 - latest
            ]);
        } catch (\Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log more details
            // Return a user-friendly error view
            return view('error.dashboard')->with('error', 'Terjadi kesalahan saat mengambil data dashboard. Silakan coba lagi nanti.');
        }
    }
}