<x-app-layout>
    <div class="max-w-6xl mx-auto p-4 space-y-4">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-dashboard.card title="Total Properti" value="{{ $totalProperties ?? 0 }}" unit="Unit" class="p-4" />
            <x-dashboard.card title="Rata-rata Harga" value="{{ $averagePriceFormatted }}" unit="" class="p-4" />
            <x-dashboard.card title="Rata-rata Luas" value="{{ $averageSize }}" unit="sqft" class="p-4" />
            <x-dashboard.card title="Belum Terverifikasi" value="{{ $propertiBelumTerverifikasi ?? 0 }}" unit="Unit" class="p-4" />
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Distribusi Harga --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-3">Distribusi Harga Properti</h3>
                <div class="h-48">
                    <canvas id="priceDistributionChart"></canvas>
                </div>
            </div>

            {{-- Status Furnishing --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-3">Status Furnishing</h3>
                <div class="h-48">
                    <canvas id="furnishingDistributionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Second Row Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Scatter Plot --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-3">Hubungan Luas vs Harga</h3>
                <div class="h-48">
                    <canvas id="sizePriceChart"></canvas>
                </div>
            </div>

            {{-- Tren Penambahan --}}
            <div class="bg-white rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold mb-3">Tren Penambahan Properti</h3>
                <div class="h-48">
                    <canvas id="addedPropertiesChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Third Row - Full Width Chart --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold mb-3">Rata-rata Harga Per Bulan</h3>
            <div class="h-64">
                <canvas id="averagePriceChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Konfigurasi default untuk semua chart
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // Price Distribution Chart
    const priceCtx = document.getElementById('priceDistributionChart').getContext('2d');
    new Chart(priceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($priceDistribution['labels']) !!},
            datasets: [{
                label: 'Jumlah Properti',
                data: {!! json_encode($priceDistribution['data']) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
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

    // Furnishing Distribution Chart
    const furnishingCtx = document.getElementById('furnishingDistributionChart').getContext('2d');
    new Chart(furnishingCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($furnishingDistribution['labels']) !!},
            datasets: [{
                data: {!! json_encode($furnishingDistribution['data']) !!},
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Size vs Price Scatter Chart
    const sizePriceCtx = document.getElementById('sizePriceChart').getContext('2d');
    new Chart(sizePriceCtx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Luas vs Harga',
                data: {!! json_encode($sizePriceData) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.6)',
                borderColor: 'rgba(16, 185, 129, 1)',
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    title: {
                        display: true,
                        text: 'Harga (AED)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'AED ' + (value / 1000000).toFixed(0) + 'M';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Luas (sqft)'
                    }
                }
            }
        }
    });

    // Added Properties Chart
    const addedCtx = document.getElementById('addedPropertiesChart').getContext('2d');
    new Chart(addedCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($addedPropertiesPerMonth['labels']) !!},
            datasets: [{
                label: 'Properti Ditambahkan',
                data: {!! json_encode($addedPropertiesPerMonth['data']) !!},
                backgroundColor: 'rgba(168, 85, 247, 0.8)',
                borderColor: 'rgba(168, 85, 247, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true
                }
            },
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
                        text: 'Tahun 2024'
                    }
                }
            }
        }
    });

    // Average Price Per Month Chart
    const averagePriceCtx = document.getElementById('averagePriceChart').getContext('2d');
    new Chart(averagePriceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($averagePricePerMonth['labels']) !!},
            datasets: [{
                label: 'Rata-rata Harga',
                data: {!! json_encode($averagePricePerMonth['data']) !!},
                backgroundColor: 'rgba(245, 101, 101, 0.1)',
                borderColor: 'rgba(245, 101, 101, 1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: 'rgba(245, 101, 101, 1)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: 'Harga (AED)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'AED ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tahun 2024'
                    }
                }
            }
        }
    });
</script>

</x-app-layout>