@extends('masterlayout')

@section('content')
<div class="container mt-3">
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="GET" action="{{ route('lead.laporan') }}">
    <div class="row ">
        <!-- CS -->
        <div class="col-12 col-md-6 mb-2">
            <select name="cs" class="form-control">
                <option value="">Semua Customer Service</option>
                @foreach($semuaUser as $user)
                    <option value="{{ $user->id }}" {{ request('cs') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jenis Lead -->
        <div class="col-12 col-md-6 mb-2">
            <select name="jenis_lead" class="form-control">
                <option value="">Semua Jenis Lead</option>
                @foreach($semuaJenisLead as $jl)
                    <option value="{{ $jl->id }}" {{ request('jenis_lead') == $jl->id ? 'selected' : '' }}>
                        {{ $jl->jenis }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date Range -->
        <div class="col-12 col-md-6 mb-2">
            <input type="text" name="daterange" id="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}">
        </div>

        <!-- Tombol Filter -->
        <div class="col-4 col-md-2 mb-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>

        <!-- Tombol Reset -->
        <div class="col-4 col-md-2 mb-2">
            <a href="{{ route('lead.laporan') }}" class="btn btn-secondary w-100">Reset</a>
        </div>

        <!-- Tombol Export -->
        <div class="col-4 col-md-2 mb-2">
            <a href="{{ route('lead.export', request()->query()) }}" class="btn btn-success w-100">Export</a>
        </div>
    </div>
</form>



<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-magnet mr-1" style="color: #00518d;"></i>Laporan Lead CS</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama User</th>
                    <th>Jumlah Lead</th>
                    <th>Jumlah Penjualan</th>
                    <th>Persentase</th>
                    <th>Jenis Lead</th>
                </tr>
            </thead>
            <tbody>
                @forelse($semuaLead as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($semuaLead->currentPage() - 1) * $semuaLead->perPage() }}</td>
                        
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                        <td>{{ $item->user->name ?? 'Tidak Diketahui' }}</td>
                        <td>{{ $item->jumlah_lead }}</td>
                        <td>{{ $item->jumlah_penjualan ?? 0 }}</td>
                        <td>
        <span class="badge {{ $item->persentase >= 30 ? 'bg-success' : 'bg-warning text-dark' }}">
            {{ $item->persentase }}%
        </span>
    </td>
<td>{{ $item->jenisLead->jenis ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data lead.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
 </div>
</div>
<!-- Pagination -->
<div class="d-flex justify-content-end mt-3">
    {{ $semuaLead->links('pagination::bootstrap-4') }}
</div>
</div>
@endsection
