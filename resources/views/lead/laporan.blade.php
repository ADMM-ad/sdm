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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-receipt mr-1" style="color: #31beb4;"></i>Laporan Lead CS</h3>
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
