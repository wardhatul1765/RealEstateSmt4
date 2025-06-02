<?php

namespace App\Http\Controllers;

use App\Models\UserProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime as MongoUTCDateTime; // Pastikan alias ini digunakan

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Harga rata-rata properti
            $averagePrice = UserProperty::avg('price');
            $averagePriceFormatted = 'AED ' . number_format($averagePrice ?? 0, 0, ',', '.');

            // Luas rata-rata properti (m² ke sqft)
            $averageSizeM2 = UserProperty::avg('sizeMin');
            $averageSizeSqft = $averageSizeM2 ? $averageSizeM2 * 10.7639 : 0;

            // Distribusi harga
            $priceDistributionRaw = UserProperty::select('price')->whereNotNull('price')->get();
            $priceDistributionData = [];
            foreach ($priceDistributionRaw as $item) {
                $range = floor($item->price / 1000000) * 1000000;
                $priceDistributionData[$range] = ($priceDistributionData[$range] ?? 0) + 1;
            }
            ksort($priceDistributionData);

            // Distribusi furnishing
            $furnishingDistributionRaw = UserProperty::raw(function ($collection) {
                return $collection->aggregate([
                    ['$match' => ['furnishing' => ['$ne' => null]]],
                    ['$group' => [
                        '_id' => '$furnishing',
                        'count' => ['$sum' => 1]
                    ]],
                    ['$sort' => ['count' => -1]]
                ]);
            });

            $furnishingDistribution = ['labels' => [], 'data' => []];
            foreach ($furnishingDistributionRaw as $item) {
                $furnishingDistribution['labels'][] = $item->_id ?? 'Unknown';
                $furnishingDistribution['data'][] = $item->count;
            }

            // Ukuran vs Harga Chart (Luas dalam sqft)
            $sizePriceData = UserProperty::select('sizeMin', 'price')
                ->whereNotNull('sizeMin')
                ->whereNotNull('price')
                ->where('sizeMin', '>', 0)
                ->where('price', '>', 0)
                ->limit(500)
                ->get()
                ->map(fn($item) => ['x' => (float) $item->sizeMin * 10.7639, 'y' => (float) $item->price])
                ->values()
                ->toArray();

            $startDate = Carbon::create(2024, 1, 1)->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            $propertiesFrom2024ToCurrent = UserProperty::whereNotNull('created_at')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Properti ditambahkan per bulan (diurutkan)
            $addedPropertiesGrouped = $propertiesFrom2024ToCurrent->groupBy(function ($item) {
                // Gunakan alias MongoUTCDateTime yang sudah diimpor
                $createdAt = $item->created_at instanceof MongoUTCDateTime
                    ? Carbon::instance($item->created_at->toDateTime())
                    : Carbon::parse($item->created_at);
                return $createdAt->format('Y-m');
            })->sortKeys();

            $addedPropertiesPerMonth = ['labels' => [], 'data' => []];
            foreach ($addedPropertiesGrouped as $month => $items) {
                $monthName = Carbon::parse($month . '-01')->format('M Y');
                $addedPropertiesPerMonth['labels'][] = $monthName;
                $addedPropertiesPerMonth['data'][] = count($items);
            }

            // Harga rata-rata properti per bulan (diurutkan)
            $avgPriceGrouped = $propertiesFrom2024ToCurrent
                ->filter(fn($item) => $item->price > 0)
                ->groupBy(function ($item) {
                    // Gunakan alias MongoUTCDateTime yang sudah diimpor
                    $createdAt = $item->created_at instanceof MongoUTCDateTime
                        ? Carbon::instance($item->created_at->toDateTime())
                        : Carbon::parse($item->created_at);
                    return $createdAt->format('Y-m');
                })->sortKeys();

            $averagePricePerMonth = ['labels' => [], 'data' => []];
            foreach ($avgPriceGrouped as $month => $items) {
                $monthName = Carbon::parse($month . '-01')->format('M Y');
                $averagePricePerMonth['labels'][] = $monthName;
                $averagePricePerMonth['data'][] = round($items->avg('price'), 0);
            }

            return view('dashboard', [
                'totalProperties' => UserProperty::count(),
                'averagePrice' => $averagePrice,
                'averagePriceFormatted' => $averagePriceFormatted,
                'averageSize' => round($averageSizeSqft, 2),
                'propertiBelumTerverifikasi' => UserProperty::where('status', 'pendingVerification')->count(),
                'priceDistribution' => [
                    'labels' => array_map(fn($range) => 'AED ' . number_format($range / 1000000, 0) . 'M', array_keys($priceDistributionData)),
                    'data' => array_values($priceDistributionData)
                ],
                'furnishingDistribution' => $furnishingDistribution,
                'sizePriceData' => $sizePriceData,
                'addedPropertiesPerMonth' => $addedPropertiesPerMonth,
                'averagePricePerMonth' => $averagePricePerMonth,
            ]);
        } catch (\Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage() . "\nStack Trace:\n" . $e->getTraceAsString());
            return view('error.dashboard')->with('error', 'Terjadi kesalahan saat mengambil data dashboard.');
        }
    }
}
