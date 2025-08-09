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
<div class="row">
    <div class="col-12">
    <div class="card card-primary card-outline" style="border-color: #00518d;">
      <div class="card-header d-flex align-items-center justify-content-between">
    <h3 class="card-title mb-0 text-nowrap flex-grow-1">Pesanan Produk</h3>
    <div class="d-flex justify-content-end">
        <form method="GET" action="{{ route('dashboard.editor') }}">
            <input type="date" name="tanggal" class="form-control form-control-sm"
                value="{{ request('tanggal', \Carbon\Carbon::today()->toDateString()) }}"
                onchange="this.form.submit()" style="max-width: 150px;">
        </form>
    </div>
</div>

        <div class="card-body">
            <div class="row">
                <!-- Diagram Sebelum Jam 11 -->
                <div class="col-md-6">
                    <h5 class="text-center">Pesanan Sebelum Jam 11 AM</h5>
                    <canvas id="chartSebelum11" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
                <!-- Diagram Setelah Jam 11 -->
                <div class="col-md-6">
                    <h5 class="text-center">Pesanan Setelah Jam 11 AM</h5>
                    <canvas id="chartSetelah11" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

    
<div class="row">
    <!-- Card Produk Yang Dikerjakan -->
    <div class="col-md-6">
        <div class="card card-primary card-outline" style="border-color: #00518d;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0 text-nowrap flex-grow-1">Produk Yang Dikerjakan</h3>
                <div class="d-flex justify-content-end">
                    <form method="GET" action="{{ route('dashboard.editor') }}">
                        <input type="date" name="tanggal" class="form-control form-control-sm"
                            value="{{ request('tanggal', \Carbon\Carbon::today()->toDateString()) }}"
                            onchange="this.form.submit()" style="max-width: 150px;">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    <!-- Card Statistik Jobdesk -->
    <div class="col-md-6">
        <div class="card card-primary card-outline" style="border-color: #00518d;">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title mb-0 text-nowrap flex-grow-1">Statistik Jobdesk</h3>
                <div class="d-flex justify-content-end">
                    <form method="GET" action="{{ route('dashboard.editor') }}">
                        <input type="date" name="tanggal" class="form-control form-control-sm"
                            value="{{ request('tanggal', \Carbon\Carbon::today()->toDateString()) }}"
                            onchange="this.form.submit()" style="max-width: 150px;">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <canvas id="statusDonutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>




</div>


@section('scripts')
@php
    $baseColors = [
        '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc',
        '#d2d6de', '#8e44ad', '#2ecc71', '#e67e22', '#1abc9c'
    ];

    $kategoriCount = $donutData->count();
    $backgroundColors = [];

    for ($i = 0; $i < $kategoriCount; $i++) {
        $backgroundColors[] = $baseColors[$i % count($baseColors)];
    }
@endphp
@php
    $baseColors = ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#007bff', '#6610f2', '#fd7e14', '#6f42c1'];

    $produkSebelum11Colors = [];
    for ($i = 0; $i < $produkSebelum11->count(); $i++) {
        $produkSebelum11Colors[] = $baseColors[$i % count($baseColors)];
    }

    $produkSetelah11Colors = [];
    for ($i = 0; $i < $produkSetelah11->count(); $i++) {
        $produkSetelah11Colors[] = $baseColors[$i % count($baseColors)];
    }
@endphp

<script>
const dataSebelum11 = {
        labels: {!! json_encode($produkSebelum11->pluck('kategori')) !!},
        datasets: [{
            label: 'Jumlah',
            data: {!! json_encode($produkSebelum11->pluck('total')) !!},
            backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#dc3545'],
        }]
    };

    const dataSetelah11 = {
        labels: {!! json_encode($produkSetelah11->pluck('kategori')) !!},
        datasets: [{
            label: 'Jumlah',
            data: {!! json_encode($produkSetelah11->pluck('total')) !!},
            backgroundColor: ['#007bff', '#6610f2', '#fd7e14', '#6f42c1'],
        }]
    };

    new Chart(document.getElementById('chartSebelum11'), {
        type: 'doughnut',
        data: dataSebelum11,
    });

    new Chart(document.getElementById('chartSetelah11'), {
        type: 'doughnut',
        data: dataSetelah11,
    });

    const donutData = {
        labels: {!! json_encode($donutData->pluck('kategori')) !!},
        datasets: [{
            data: {!! json_encode($donutData->pluck('total')) !!},
            backgroundColor: {!! json_encode($backgroundColors) !!},
        }]
    };

    const donutOptions = {
        maintainAspectRatio : false,
        responsive : true,
    };

    new Chart(document.getElementById('donutChart').getContext('2d'), {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    });

    const statusDonutData = {
        labels: {!! json_encode($donutStatusData->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($donutStatusData->pluck('total')) !!},
            backgroundColor: ['#28a745', '#ffc107'], // selesai = hijau, onproses = kuning
        }]
    };

    const statusDonutConfig = {
        type: 'doughnut',
        data: statusDonutData,
        options: {
            responsive: true,
        }
    };

    new Chart(
        document.getElementById('statusDonutChart'),
        statusDonutConfig
    );
</script>
@endsection


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

@endsection
