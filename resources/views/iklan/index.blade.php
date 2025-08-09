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


<div class="row mb-2">
    <div class="col-md-12">
        <form method="GET" action="{{ route('kampanye.index') }}">
            <div class="form-row">
                <!-- Dropdown Customer Service -->
                <div class="col-12 col-lg-4 mb-2">
                    <select name="user_id" class="form-control">
                        <option value="">Cari Customer Service</option>
                        @foreach($customerservices as $cs)
                            <option value="{{ $cs->id }}" {{ $selectedUser == $cs->id ? 'selected' : '' }}>
                                {{ $cs->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Kode Kampanye -->
                <div class="col-12 col-lg-4 mb-2">
                    <input type="text" name="search" class="form-control"
                           placeholder="Cari Kode Kampanye..."
                           value="{{ $searchKode }}">
                </div>

                <!-- Tombol Filter -->
                <div class="col-6 col-lg-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #00518d; border-color: #00518d;">
                        Filter
                    </button>
                </div>

                <!-- Tombol Reset -->
                <div class="col-6 col-lg-2 mb-2">
                    <a href="{{ route('kampanye.index') }}" class="btn btn-secondary w-100">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>



<!-- Tabel Kampanye -->
<div class="row">
    <div class="col-12">
        <div class="card">
             <div class="card-header d-flex justify-content-end align-items-center">
    <h3 class="card-title mb-0 mr-auto">
        <i class="fas fa-bullhorn mr-1" style="color: #00518d;"></i>Daftar Kode Kampanye
    </h3>
    <a href="{{ route('kampanye.create') }}" class="btn-sm" style="background-color: #00518d;">
        <i class="fas fa-plus-circle" style="color: #ffffff;"></i>
    </a>
</div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Kode Kampanye</th>
                            <th>Jenis Lead</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kampanye as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td>{{ $item->kode_kampanye }}</td>
                                <td>
                {{ $item->jenisLead ? $item->jenisLead->jenis : '-' }}
            </td>
                                <td>
                                    <a href="{{ route('kampanye.edit', $item->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>

                                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('kampanye.destroy', $item->id) }}')"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data kampanye</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
