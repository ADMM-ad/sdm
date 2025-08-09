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

    <form method="GET" action="{{ route('dashboard.customerservice') }}" class="row mb-3 justify-content-md-end">
        <div class="col-md-4 col-12 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{  $daterange ?? '' }}">
        </div>

        <div class="col-md-2 col-12">
            <div class="row">
                <div class="col-6">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #00518d; border-color: #00518d;">Filter</button>
                </div>
                <div class="col-6">
                    <a href="{{ route('dashboard.customerservice') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </div>
    </form>
<div class="row ">
    <!-- Total Penjualan -->
<div class="col-md-4">
    <div class="card  card-primary card-outline" style="border-color: #28A745;">
        <div class="small-box  d-flex align-items-center m-0" style="height: 100px; border: none;">
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
        <div class="small-box  d-flex align-items-center m-0 " style="height: 100px; border: none;">
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


</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(function() {
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Batal',
            applyLabel: 'Terapkan',
            format: 'YYYY-MM-DD'
        }
    });

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
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
@endsection
