@extends('masterlayout')

@section('content')
<div class="container mt-3">
    

    <form method="GET" action="{{ route('dashboard.admin') }}" class="row mb-3 justify-content-md-end">
    <!-- Input Rentang Tanggal -->
    <div class="col-md-4 col-12 mb-2">
        <input type="text" id="daterange" name="daterange" class="form-control"
               placeholder="Pilih rentang tanggal"
               value="{{ request('daterange') }}">
    </div>

    <!-- Tombol Filter dan Reset -->
    <div class="col-md-2 col-12">
        <div class="row">
            <div class="col-6">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-6">
                <a href="{{ route('dashboard.admin') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </div>
    </div>
</form>


    <div class="row mt-3">
        <!-- Card Total Penjualan -->
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Closing</h5>
                    <p class="card-text display-5">{{ $jumlahPenjualan }}</p>
                </div>
            </div>
        </div>

<div class="col-md-4">
    <div class="card text-white bg-success mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Lead</h5>
            <p class="card-text display-5">{{ $totalLead }}</p>
        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card text-white bg-warning mb-3">
        <div class="card-body">
            <h5 class="card-title">Total HPP</h5>
            <p class="card-text display-5">Rp{{ number_format($totalHpp, 0, ',', '.') }}</p>
        </div>
    </div>
</div>
<!-- Card Total Omset -->
<div class="col-md-4">
    <div class="card text-white bg-dark mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Omset</h5>
            <p class="card-text display-5">Rp{{ number_format($totalOmset, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Card Biaya COD -->
<div class="col-md-4">
    <div class="card text-white bg-danger mb-3">
        <div class="card-body">
            <h5 class="card-title">Biaya COD</h5>
            <p class="card-text display-5">Rp{{ number_format($totalBiayaCod, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Card Ongkir -->
<div class="col-md-4">
    <div class="card text-white bg-info mb-3">
        <div class="card-body">
            <h5 class="card-title">Biaya Ongkir</h5>
            <p class="card-text display-5">Rp{{ number_format($totalOngkir, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Card Cashback -->
<div class="col-md-4">
    <div class="card text-white bg-secondary mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Cashback</h5>
            <p class="card-text display-5">Rp{{ number_format($totalCashback, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<!-- Card Total Iklan -->
<div class="col-md-4">
    <div class="card text-white bg-primary mb-3">
        <div class="card-body">
            <h5 class="card-title">Total Iklan</h5>
            <p class="card-text display-5">Rp{{ number_format($totalDibelanjakan, 0, ',', '.') }}</p>
        </div>
    </div>
</div>


<!-- Card Profit -->
<div class="col-md-4">
    <div class="card text-white bg-success mb-3">
        <div class="card-body">
            <h5 class="card-title">Profit</h5>
            <p class="card-text display-5">Rp{{ number_format($profit, 0, ',', '.') }}</p>
        </div>
    </div>
</div>


    </div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-magnet mr-1" style="color: #31beb4;"></i>Laporan Lead</h3>
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
        <div class="card">
            <div class="card-header d-flex  align-items-center flex-wrap flex-md-nowrap">
                <h3 class="card-title mb-2 mb-md-0">
                    <i class="fas fa-magnet mr-1" style="color: #31beb4;"></i>Lead Harian
                </h3>
                <form method="GET" class="form-inline ml-2">
                    <div class="form-group">
                        <input type="text" name="filter_tanggal" id="filter_tanggal" class="form-control"
                               placeholder="Pilih rentang tanggal" value="{{ request('filter_tanggal', $filterTanggal) }}">
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
@endsection
