<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total properti
        $totalProperties = Property::count();

        // Rata-rata harga properti
        $averagePrice = Property::avg('price');

        // Rata-rata luas properti
        $averageSize = Property::avg('sizeMin');

        // Properti belum terverifikasi (bukan yang verified)
        $propertiBelumTerverifikasi = Property::where('verified', false)->count();

        // Distribusi harga
        $priceRanges = [
            '< 500K' => Property::where('price', '<', 500000)->count(),
            '500K - 1M' => Property::whereBetween('price', [500000, 999999])->count(),
            '1M - 1.5M' => Property::whereBetween('price', [1000000, 1499999])->count(),
            '1.5M - 2M' => Property::whereBetween('price', [1500000, 1999999])->count(),
            '> 2M' => Property::where('price', '>=', 2000000)->count(),
        ];
        $priceDistribution = [
            'labels' => array_keys($priceRanges),
            'data' => array_values($priceRanges),
        ];

        // Scatter: Luas vs Harga
        $sizePriceData = Property::select('sizeMin', 'price')->get()->map(function ($item) {
            $size = (float) ($item->sizeMin ?? 0);
            $price = (int) ($item->price ?? 0);
            return ['x' => $size, 'y' => $price];
        })->toArray();

        // Penambahan properti per bulan
        $addedRaw = Property::raw(function ($collection) {
            return $collection->aggregate([
                ['$addFields' => [
                    'addedOnDate' => [
                        '$cond' => [
                            ['$ne' => ['$addedOn', null]],
                            ['$toDate' => '$addedOn'],
                            null
                        ]
                    ]
                ]],
                ['$match' => ['addedOnDate' => ['$ne' => null]]],
                ['$group' => [
                    '_id' => [
                        'year' => ['$year' => '$addedOnDate'],
                        'month' => ['$month' => '$addedOnDate']
                    ],
                    'count' => ['$sum' => 1],
                ]],
                ['$sort' => ['_id.year' => 1, '_id.month' => 1]],
            ]);
        });

        $addedPropertiesPerMonth = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($addedRaw as $item) {
            if (is_array($item->_id) && isset($item->_id['year']) && isset($item->_id['month'])) {
                $year = $item->_id['year'];
                $monthNum = $item->_id['month'];
                if ($year > 0 && $monthNum >= 1 && $monthNum <= 12) {
                    $monthName = Carbon::create($year, $monthNum)->format('M Y');
                } else {
                    $monthName = 'Invalid Date';
                }
            } else {
                $monthName = 'Unknown Date';
            }
            $addedPropertiesPerMonth['labels'][] = $monthName;
            $addedPropertiesPerMonth['data'][] = $item->count;
        }

        // Harga yang Paling Sering Muncul per Bulan dan Tahun
        $mostFrequentPriceRaw = Property::raw(function ($collection) {
            return $collection->aggregate([
                ['$addFields' => [
                    'addedOnDate' => [
                        '$cond' => [
                            ['$ne' => ['$addedOn', null]],
                            ['$toDate' => '$addedOn'],
                            null
                        ]
                    ]
                ]],
                ['$match' => ['addedOnDate' => ['$ne' => null]]],
                ['$group' => [
                    '_id' => [
                        'year' => ['$year' => '$addedOnDate'],
                        'month' => ['$month' => '$addedOnDate'],
                        'price' => '$price'
                    ],
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => [
                    '_id.year' => 1,
                    '_id.month' => 1,
                    'count' => -1 // Urutkan berdasarkan frekuensi terbanyak
                ]],
                ['$group' => [
                    '_id' => [
                        'year' => '$_id.year',
                        'month' => '$_id.month'
                    ],
                    'mostFrequentPrice' => ['$first' => '$_id.price'], // Ambil harga paling sering muncul
                    'frequency' => ['$first' => '$count'] // (Opsional) frekuensinya
                ]],
                ['$sort' => [
                    '_id.year' => 1,
                    '_id.month' => 1
                ]]
            ]);
        });

        $mostFrequentPricePerMonth = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($mostFrequentPriceRaw as $item) {
            if (is_array($item->_id) && isset($item->_id['year']) && isset($item->_id['month'])) {
                $year = $item->_id['year'];
                $monthNum = $item->_id['month'];
                if ($year > 0 && $monthNum >= 1 && $monthNum <= 12) {
                    $monthName = Carbon::create($year, $monthNum)->format('M Y');
                } else {
                    $monthName = 'Invalid Date';
                }
            } else {
                $monthName = 'Unknown Date';
            }
            $mostFrequentPricePerMonth['labels'][] = $monthName;
            $mostFrequentPricePerMonth['data'][] = $item->mostFrequentPrice;
        }


        // Furnishing distribution
        $furnishingData = Property::raw(function ($collection) {
            return $collection->aggregate([
                ['$group' => [
                    '_id' => '$furnishing',
                    'count' => ['$sum' => 1],
                ]],
                ['$sort' => ['_id' => 1]]
            ]);
        });

        $furnishingDistribution = [
            'labels' => [],
            'data' => [],
        ];

        foreach ($furnishingData as $item) {
            $label = $item->_id ?? 'Tidak Diketahui';
            $furnishingDistribution['labels'][] = $label;
            $furnishingDistribution['data'][] = $item->count;
        }

        return view('dashboard', compact(
            'totalProperties',
            'averagePrice',
            'averageSize',
            'propertiBelumTerverifikasi',
            'priceDistribution',
            'sizePriceData',
            'addedPropertiesPerMonth',
            'furnishingDistribution',
            'mostFrequentPricePerMonth' // Tambahkan ini
        ));
    }
}