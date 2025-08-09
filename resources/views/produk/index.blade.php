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



<!-- Form Search -->
<div class="row mb-3">
    <div class="col-md-12 mb-1">
        <form method="GET" action="{{ route('produk.index') }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama Produk..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary" style="background-color: #00518d; border-color: #00518d;">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Tabel Produk -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-end align-items-center">
    <h3 class="card-title mb-0 mr-auto">
        <i class="fas fa-box mr-1" style="color: #00518d;"></i>Daftar Produk
    </h3>
    <a href="{{ route('produk.create') }}" class="btn-sm" style="background-color: #00518d;">
        <i class="fas fa-plus-circle" style="color: #ffffff;"></i>
    </a>
</div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Detail Produk</th>
                            <th>HPP</th>
                            <th>Harga Jual</th>
                            <th>Kategori Produk</th>
                            <th>Jenis Lead</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama_produk }}</td>
                                <td>{{ $item->detail_produk }}</td>
                                <td>Rp {{ number_format($item->hpp, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ $item->jenisProduk?->kategori ?? '-' }}</td>
                                <td>{{ $item->jenisLead?->jenis ?? '-' }}</td>

                                <td>
                                    <a href="{{ route('produk.edit', $item->id) }}" class="btn btn-warning btn-sm"> <i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('produk.destroy', $item->id) }}')"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-end mt-3">
    {{ $produk->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
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
