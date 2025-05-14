<x-app-layout>
    <div class="space-y-6">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Contoh Card --}}
            <x-dashboard.card title="Jumlah Properti Terdaftar" value="{{ $totalProperties }}" unit="Unit" />
            <x-dashboard.card title="Jumlah Kunjungan Properti" value="{{ $totalViews }}" unit="Views" />
            <x-dashboard.card title="Properti Terjual Bulan Ini" value="{{ $soldThisMonth }}" unit="Unit" />
            <x-dashboard.card title="Agen Aktif" value="{{ $activeAgents }}" unit="Agen" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Bar Chart --}}
            <x-dashboard.chart title="Penjualan Per-Bulan">
                <canvas id="salesChart" class="h-64"></canvas>
            </x-dashboard.chart>

            <div class="space-y-6">
                {{-- Pie Chart --}}
                <x-dashboard.chart title="Properti Terjual Per-Area">
                    <canvas id="soldPropertyChart" class="h-48"></canvas>
                </x-dashboard.chart>

                {{-- Line Chart --}}
                <x-dashboard.chart title="Tren Pengunjung Website">
                    <canvas id="visitorChart" class="h-48"></canvas>
                </x-dashboard.chart>
            </div>
        </div>
    </div>

    {{-- Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const visitorCtx = document.getElementById('visitorChart').getContext('2d');
        const soldCtx = document.getElementById('soldPropertyChart').getContext('2d');

        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesPerMonth['labels']) !!},
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: {!! json_encode($salesPerMonth['data']) !!},
                    backgroundColor: '#3b82f6',
                }]
            }
        });

        new Chart(visitorCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($visitors['labels']) !!},
                datasets: [{
                    label: 'Pengunjung',
                    data: {!! json_encode($visitors['data']) !!},
                    borderColor: '#10b981',
                    fill: false
                }]
            }
        });

        new Chart(soldCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($soldDistribution['labels']) !!},
                datasets: [{
                    data: {!! json_encode($soldDistribution['data']) !!},
                    backgroundColor: ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
                }]
            }
        });
    </script>
</x-app-layout>
dashboard