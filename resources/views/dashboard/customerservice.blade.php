@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <form method="GET" action="{{ route('dashboard.customerservice') }}" class="row mb-3 justify-content-md-end">
        <div class="col-md-4 col-12 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{ request('daterange') }}">
        </div>

        <div class="col-md-2 col-12">
            <div class="row">
                <div class="col-6">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-6">
                    <a href="{{ route('dashboard.customerservice') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </div>
    </form>
<div class="row mt-3">
    <!-- Total Penjualan -->
<div class="col-md-4">
    <div class="card bg-primary text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Closing</h5>
            <p class="card-text display-5">{{ $jumlahPenjualan }}</p>
        </div>
    </div>
</div>

<!-- Total Lead -->
<div class="col-md-4">
    <div class="card bg-success text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Lead</h5>
            <p class="card-text display-5">{{ $totalLead }}</p>
        </div>
    </div>
</div>

<!-- Total HPP -->
<div class="col-md-4">
    <div class="card bg-warning text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total HPP</h5>
            <p class="card-text display-5">Rp{{ number_format($totalHpp, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Total Omset -->
<div class="col-md-4">
    <div class="card bg-dark text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Omset</h5>
            <p class="card-text display-5">Rp{{ number_format($totalOmset, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Biaya COD -->
<div class="col-md-4">
    <div class="card bg-danger text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Biaya COD</h5>
            <p class="card-text display-5">Rp{{ number_format($totalBiayaCod, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Ongkir -->
<div class="col-md-4">
    <div class="card bg-info text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Biaya Ongkir</h5>
            <p class="card-text display-5">Rp{{ number_format($totalOngkir, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Cashback -->
<div class="col-md-4">
    <div class="card bg-secondary text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Cashback</h5>
            <p class="card-text display-5">Rp{{ number_format($totalCashback, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Total Iklan -->
<div class="col-md-4">
    <div class="card bg-primary text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Iklan</h5>
            <p class="card-text display-5">Rp{{ number_format($totalDibelanjakan, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Profit -->
<div class="col-md-4">
    <div class="card bg-success text-white mb-3">
        <div class="card-body">
            <h5 class="card-title">Profit</h5>
            <p class="card-text display-5">Rp{{ number_format($profit, 0, ',', '.') }}</p>
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
@endsection
