<x-app-layout>
    <div class="space-y-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-dashboard.card title="Total Properti" value="{{ $totalProperties ?? 0 }}" unit="Unit" />
            <x-dashboard.card title="Rata-rata Harga Properti" value="{{ 'AED ' . number_format($averagePrice ?? 0, 0, ',', '.') }}" unit="" />
            <x-dashboard.card title="Rata-rata Luas Properti" value="{{ round($averageSize ?? 0, 2) }}" unit="m²" />
            <x-dashboard.card title="Properti Belum Terverifikasi" value="{{ $propertiBelumTerverifikasi ?? 0 }}" unit="Unit" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Histogram Distribusi Harga Properti --}}
            <x-dashboard.chart title="Distribusi Harga Properti">
                <canvas id="priceDistributionChart" class="h-64"></canvas>
            </x-dashboard.chart>

            <div class="space-y-6">
                {{-- Scatter Plot Luas vs Harga --}}
                <x-dashboard.chart title="Hubungan Luas vs Harga">
                    <canvas id="sizePriceChart" class="h-48"></canvas>
                </x-dashboard.chart>

                {{-- Bar Chart Tren Penambahan Properti Per Bulan --}}
                <x-dashboard.chart title="Tren Penambahan Properti Per Bulan">
                    <canvas id="addedPropertiesChart" class="h-48"></canvas>
                </x-dashboard.chart>

                {{-- Pie Chart Distribusi Furnishing --}}
                <x-dashboard.chart title="Distribusi Status Furnishing">
                    <canvas id="furnishingDistributionChart" class="h-48"></canvas>
                </x-dashboard.chart>
            </div>

            {{-- Line Chart Harga yang Sering Muncul --}}
                <x-dashboard.chart title="Harga yang Sering Muncul">
                  <canvas id="mostFrequentPriceChart" class="h-72"></canvas>
                </x-dashboard.chart>

        </div>
    </div>

    {{-- Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const priceCtx = document.getElementById('priceDistributionChart').getContext('2d');
        const sizePriceCtx = document.getElementById('sizePriceChart').getContext('2d');
        const addedCtx = document.getElementById('addedPropertiesChart').getContext('2d');
        const furnishingCtx = document.getElementById('furnishingDistributionChart').getContext('2d');
        const mostFrequentPriceCtx = document.getElementById('mostFrequentPriceChart').getContext('2d');

        new Chart(priceCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($priceDistribution['labels']) !!},
                datasets: [{
                    label: 'Jumlah Properti',
                    data: {!! json_encode($priceDistribution['data']) !!},
                    backgroundColor: '#4CAF50',
                    borderColor: '#388E3C',
                    borderWidth: 1,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Properti'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Rentang Harga'
                        }
                    }
                }
            }
        });

        new Chart(sizePriceCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Luas vs Harga Properti',
                    data: {!! json_encode($sizePriceData) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    pointRadius: 5,
                }]
            },
            options: {
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Harga'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Luas (m²)'
                        }
                    }
                }
            }
        });

        new Chart(addedCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($addedPropertiesPerMonth['labels']) !!},
                datasets: [{
                    label: 'Properti Ditambahkan',
                    data: {!! json_encode($addedPropertiesPerMonth['data']) !!},
                    backgroundColor: '#68d391',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Properti Ditambahkan'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });

        new Chart(furnishingCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($furnishingDistribution['labels']) !!},
                datasets: [{
                    data: {!! json_encode($furnishingDistribution['data']) !!},
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                }]
            }
        });

        new Chart(mostFrequentPriceCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($mostFrequentPricePerMonth['labels']) !!},
                datasets: [{
                    label: 'Harga Properti (Paling Sering Muncul)',
                    data: {!! json_encode($mostFrequentPricePerMonth['data']) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Harga (AED)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>