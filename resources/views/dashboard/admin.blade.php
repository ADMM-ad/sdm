@extends('masterlayout')

@section('content')
<style>
  .fade-out {
    animation: fadeOut 1s forwards;
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; visibility: hidden; }
}

</style>
@if(session('welcome'))
<div id="welcome-message" class="callout callout-info position-fixed" style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
    <button type="button" class="close" onclick="closeWelcome()" style="position: absolute; top: 10px; right: 10px;">&times;</button>
    <h5>Selamat Datang, {{ Auth::user()->name }}</h5>
    <div id="countdown" style="position: absolute; bottom: 5px; right: 10px; font-size: 14px; color: #666;"></div>
</div>
@endif

<div class="container mt-3">
    

   <form method="GET" action="{{ route('dashboard.admin') }}" class="row mb-3">

    <!-- Filter Jenis Lead -->
    <div class="col-md-4 col-12 mb-2">
        <select name="jenis_lead_id" class="form-control">
            <option value="">-- Semua Jenis Lead --</option>
            @foreach(\App\Models\JenisLead::all() as $jenis)
                <option value="{{ $jenis->id }}" {{ request('jenis_lead_id') == $jenis->id ? 'selected' : '' }}>
                    {{ $jenis->jenis }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Input Rentang Tanggal -->
    <div class="col-md-4 col-12 mb-2">
        <input type="text" id="daterange" name="daterange" class="form-control"
               placeholder="Pilih rentang tanggal"
               value="{{ request('daterange') }}">
    </div>

    <!-- Tombol Filter -->
    <div class="col-md-2 col-6 mb-2">
        <button type="submit" class="btn btn-primary w-100" style="background-color: #00518d; border-color: #00518d;">Filter</button>
    </div>

    <!-- Tombol Reset -->
    <div class="col-md-2 col-6 mb-2">
        <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary w-100">Reset</a>
    </div>

</form>



    <div class="row mt-3">
     <!-- Total Closing -->
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #28A745;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">Total Closing</h5>
                <p class="card-text display-5">{{ $jumlahPenjualan }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-shopping-cart" style="color: #28A745;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Total Lead -->
<div class="col-md-4">
    <div class="card card-success card-outline" style="border-color: #33B5FF;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">Total Lead</h5>
                <p class="card-text display-5">{{ $totalLead }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-magnet" style="color: #33B5FF;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Total HPP -->
<div class="col-md-4">
    <div class="card card-warning card-outline" style="border-color: #ffc107;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">Total HPP</h5>
                <p class="card-text display-5">Rp{{ number_format($totalHpp, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-warehouse" style="color: #FFC107;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Total Omset -->
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #85BB65;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">Total Omset</h5>
                <p class="card-text display-5">Rp{{ number_format($totalOmset, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-money-bill-wave" style="color: #85BB65;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Biaya COD -->
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #dc3545;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1 ">
                <h5 class="card-title">Biaya COD</h5>
                <p class="card-text display-5">Rp{{ number_format($totalBiayaCod, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-hand-holding-usd" style="color: #dc3545;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Ongkir -->
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #795548;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">Biaya Ongkir</h5>
                <p class="card-text display-5">Rp{{ number_format($totalOngkir, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-shipping-fast" style="color: #795548;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Cashback -->
<div class="col-md-4">
    <div class="card card-secondary card-outline" style="border-color: #FF5733;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1 ">
                <h5 class="card-title">Total Cashback</h5>
                <p class="card-text display-5">Rp{{ number_format($totalCashback, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-coins" style="color: #FF5733;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Total Iklan -->
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #007bff;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1 ">
                <h5 class="card-title">Total Iklan</h5>
                <p class="card-text display-5">Rp{{ number_format($totalDibelanjakan, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-bullhorn" style="color: #007bff;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Profit -->
<div class="col-md-4">
    <div class="card card-success card-outline" style="border-color: #00BCD4;">
        <div class="small-box d-flex align-items-center m-0 " style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1 ">
                <h5 class="card-title">Profit</h5>
                <p class="card-text display-5">Rp{{ number_format($profit, 0, ',', '.') }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-money-check-alt" style="color: #00BCD4;"></i>
            </div>
        </div>
    </div>
</div>

{{-- CPR --}}
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #D3D3D3;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">CPR</h5>
                <p class="card-text display-5">{{ number_format($cpr, 2) }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-balance-scale" style="color: #D3D3D3;"></i>
            </div>
        </div>
    </div>
</div>

{{-- ROI --}}
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #FFD700;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">ROI</h5>
                <p class="card-text display-5">{{ number_format($roi, 2) }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-dollar-sign" style="color: #FFD700;"></i>
            </div>
        </div>
    </div>
</div>

{{-- ROAS --}}
<div class="col-md-4">
    <div class="card card-primary card-outline" style="border-color: #6F42C1;">
        <div class="small-box d-flex align-items-center m-0" style="height: 100px; border: none;">
            <div class="inner text-start ps-3 flex-grow-1">
                <h5 class="card-title">ROAS</h5>
                <p class="card-text display-5">{{ number_format($roas, 2) }}</p>
            </div>
            <div class="icon pe-3">
                <i class="fas fa-chart-line" style="color: #6F42C1;"></i>
            </div>
        </div>
    </div>
</div>


    </div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-magnet mr-1" style="color: #00518d;"></i>Laporan Lead</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
            <thead>
            <tr>
                <th>Nama CS</th>
                <th>Total Lead</th>
                <th>Total Penjualan</th>
                <th>Persentase Closing</th>
                <th>Total HPP</th>
                <th>Total Omset</th>
                <th>Biaya COD</th>
<th>Biaya Ongkir</th>
<th>Cashback</th>
<th>Total Iklan</th>
<th>Profit</th>
<th>Cost/Closing</th>
<th>Cost/Lead</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataCSAllTime as $cs)
            <tr>
                <td>{{ $cs['nama'] }}</td>
                <td>{{ $cs['lead'] }}</td>
                <td>{{ $cs['penjualan'] }}</td>
                <td>
                    <span class="badge {{ $cs['persentase'] >= 30 ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $cs['persentase'] }}%
                    </span>
                </td>
                <td>Rp{{ number_format($cs['total_hpp'], 0, ',', '.') }}</td>
                <td>Rp{{ number_format($cs['total_omset'], 0, ',', '.') }}</td>
                <td>Rp{{ number_format($cs['total_biaya_cod'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_ongkir'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_cashback'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_iklan'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['profit'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['costclosing'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['costlead'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
</div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline mb-4" style="border-color: #00518d;" >
            <div class="card-header">
                <div class="row w-100 align-items-center"">
                <div class="col-md-4">
                        <h3 class="card-title"><i class="fas fa-chart-line mr-1" style="color: #00518d;"></i>Grafik Keuangan Bulanan</h3>
                    </div>

                <!-- Baris untuk dropdown dan filter bulan -->
                
<div class="col-md-4 mb-2">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="dropdownMetric" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Pilih Perhitungan
        </button>
        <div class="dropdown-menu p-2" aria-labelledby="dropdownMetric" style="max-height: 300px; overflow-y: auto; min-width: 250px;">
            @foreach(['profit', 'total_bayar', 'total_hpp', 'ongkir', 'biaya_cod', 'cashback', 'iklan'] as $metric)
                <div class="form-check dropdown-item">
                    <input class="form-check-input metric-toggle" type="checkbox" value="{{ $metric }}" id="check_{{ $metric }}" checked>
                    <label class="form-check-label" for="check_{{ $metric }}">
                        {{ ucfirst(str_replace('_', ' ', $metric)) }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>


                    <!-- Filter bulan -->
                    <div class="col-md-4 mb-2">
                        <form method="GET" action="{{ route('dashboard.admin') }}">
                            <input type="month"
                                   name="bulan"
                                   class="form-control"
                                   value="{{ request('bulan', now()->format('Y-m')) }}"
                                   onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
            </div>

           <div class="card-body">
    <div style="height: 400px;">
        <canvas id="lineChart"></canvas>
    </div>
</div>
        </div>
    </div>
</div>








<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline" style="border-color: #00518d;">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h3 class="card-title mb-2 mb-md-0">
                <i class="fas fa-magnet mr-1" style="color: #00518d;"></i> Lead Harian
            </h3>

            <form method="GET" class="form-inline ml-auto">
                <div class="form-group">
                    <input type="text" name="filter_tanggal" id="filter_tanggal" class="form-control"
                           placeholder="Pilih rentang tanggal"
                           value="{{ request('filter_tanggal', $filterTanggal) }}">
                    <button type="submit" class="btn btn-primary ml-2">Tampilkan</button>
                </div>
            </form>
        </div>
    


            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
            <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama CS</th>
                <th>Total Lead</th>
                <th>Total Penjualan</th>
                <th>Persentase Closing</th>
                <th>Total HPP</th>
                <th>Total Omset</th>
                <th>Biaya COD</th>
<th>Biaya Ongkir</th>
<th>Cashback</th>
<th>Total Iklan</th>
<th>Profit</th>
<th>Cost/Closing</th>
<th>Cost/Lead</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyData as $tanggal => $csList)
                @foreach($csList as $cs)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</td>
                    <td>{{ $cs['nama'] }}</td>
                    <td>{{ $cs['lead'] }}</td>
                    <td>{{ $cs['penjualan'] }}</td>
                    <td>
                        <span class="badge {{ $cs['persentase'] >= 30 ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $cs['persentase'] }}%
                        </span>
                    </td>
                    <td>Rp{{ number_format($cs['total_hpp'], 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($cs['total_omset'], 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($cs['total_biaya_cod'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_ongkir'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_cashback'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['total_iklan'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['profit'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['costclosing'], 0, ',', '.') }}</td>
<td>Rp{{ number_format($cs['costlead'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
</div>
</div>
</div>




</div>
@endsection

@section('scripts')
<!-- Tambahkan script daterangepicker jika belum ada -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>



  new Litepicker({
        element: document.getElementById('filter_tanggal'),
        singleMode: false,
        autoApply: true,
        format: 'YYYY-MM-DD',
        dropdowns: {
            minYear: 2020,
            maxYear: new Date().getFullYear(),
            months: true,
            years: true
        }
    });
</script>




@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartLabels = {!! json_encode($lineLabels) !!};
    const chartDataMap = {!! json_encode($lineMetrics) !!};

    const metricColors = {
        'profit': { border: '#28a745', background: 'rgba(40, 167, 69, 0.2)' },
        'total_bayar': { border: '#007bff', background: 'rgba(0, 123, 255, 0.2)' },
        'total_hpp': { border: '#ffc107', background: 'rgba(255, 193, 7, 0.2)' },
        'ongkir': { border: '#17a2b8', background: 'rgba(23, 162, 184, 0.2)' },
        'biaya_cod': { border: '#fd7e14', background: 'rgba(253, 126, 20, 0.2)' },
        'cashback': { border: '#6f42c1', background: 'rgba(111, 66, 193, 0.2)' },
        'iklan': { border: '#dc3545', background: 'rgba(220, 53, 69, 0.2)' }
    };

    const ctx = document.getElementById('lineChart').getContext('2d');

    function getDatasets(selectedMetrics) {
        return selectedMetrics.map(metric => ({
            label: metric.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
            data: chartDataMap[metric],
            borderColor: metricColors[metric].border,
            backgroundColor: metricColors[metric].background,
            tension: 0.3,
            fill: true,
            pointRadius: 3
        }));
    }

    function updateChartScale(selectedMetrics) {
        let allValues = [];
        selectedMetrics.forEach(metric => {
            allValues = allValues.concat(chartDataMap[metric]);
        });

        const min = Math.min(...allValues);
        const max = Math.max(...allValues);
        const range = max - min;
        const step = range / 4;

        lineChart.options.scales.y.min = min;
        lineChart.options.scales.y.max = max;
        lineChart.options.scales.y.ticks.stepSize = step;
    }

    const initialMetrics = Object.keys(chartDataMap);
    const chartConfig = {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: getDatasets(initialMetrics)
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    };

    const lineChart = new Chart(ctx, chartConfig);
    updateChartScale(initialMetrics);

    document.querySelectorAll('.metric-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const selectedMetrics = Array.from(document.querySelectorAll('.metric-toggle:checked')).map(cb => cb.value);

            lineChart.data.datasets = getDatasets(selectedMetrics);
            updateChartScale(selectedMetrics);
            lineChart.update();
        });
    });
});
</script>

@if(session('welcome'))
<script>
    let count = 3;
    const countdownEl = document.getElementById('countdown');
    const welcomeEl = document.getElementById('welcome-message');

    function updateCountdown() {
        if (count > 0) {
            countdownEl.textContent = 'Menghilang dalam ' + count + '...';
            count--;
            setTimeout(updateCountdown, 1000);
        } else {
            countdownEl.textContent = '';
            welcomeEl.classList.add('fade-out');
            setTimeout(() => {
                welcomeEl.style.display = 'none';
            }, 1000); // durasi fadeOut
        }
    }

    function closeWelcome() {
        welcomeEl.style.display = 'none';
    }

    // Jalankan countdown saat halaman load
    window.onload = updateCountdown;
</script>
@endif

<script>



  new Litepicker({
        element: document.getElementById('filter_tanggal'),
        singleMode: false,
        autoApply: true,
        format: 'YYYY-MM-DD',
        dropdowns: {
            minYear: 2020,
            maxYear: new Date().getFullYear(),
            months: true,
            years: true
        }
    });
</script>
@endsection




@endsection
