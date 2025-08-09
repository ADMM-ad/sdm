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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<form method="GET" action="{{ route('lead.index') }}" class="mb-3">
    <div class="row">
        <div class="col-12 col-md-4 col-lg-4 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{ request('daterange') }}">
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-2">
            <select name="jenis_lead" class="form-control">
                <option value="">Semua Jenis Lead</option>
                @foreach($semuaJenisLead as $jl)
                    <option value="{{ $jl->id }}" {{ request('jenis_lead') == $jl->id ? 'selected' : '' }}>
                        {{ $jl->jenis }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2 col-lg-2 mb-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-6 col-md-2 col-lg-2 mb-2">
            <a href="{{ route('lead.index') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>



<!-- Tabel Lead -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice mr-1" style="color: #00518d;"></i>Laporan Lead Anda</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                             <th>Tanggal</th>
                            <th>Jumlah Lead</th>
                           
                            <th>Jumlah Penjualan</th>
<th>Persentase</th>
<th>Jenis Lead</th>

                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lead as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                <td>{{ $item->jumlah_lead }}</td>
                                <td>{{ $item->jumlah_penjualan }}</td>
<td>
    <span class="badge {{ $item->persentase >= 30 ? 'bg-success' : 'bg-warning text-dark' }}">
        {{ $item->persentase }}%
    </span>
</td>
<td>{{ $item->jenisLead?->jenis ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('lead.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('lead.destroy', $item->id) }}')"><i class="fas fa-trash-alt"></i> </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data lead.</td>
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
    {{ $lead->links('pagination::bootstrap-4') }}
</div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(action) {
        var form = document.getElementById('deleteForm');
        form.action = action;
        $('#confirmDeleteModal').modal('show');
    }
</script>
@endsection
