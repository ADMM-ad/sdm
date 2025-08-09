@extends('masterlayout')

@section('content')
<div class="container mt-4">
    <h4>Import Data Penjualan</h4>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


    <form action="{{ route('penjualan.importLama') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="file">Upload File Excel (.xlsx)</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Import</button>
        <a href="{{ route('penjualan.download-template') }}" class="btn btn-success mt-3">
    Download Template XLSX
</a>

    </form>

   <form action="{{ route('penjualan.hitungOmsetSemua') }}" method="POST" onsubmit="return confirm('Yakin ingin menghitung omset untuk semua penjualan?');">
    @csrf
    <button type="submit" class="btn btn-warning">
        Hitung Semua Hasil Pembagian Omset
    </button>
</form>
<!-- <a href="{{ route('penjualan.resetHpp') }}" class="btn btn-warning">
    Reset Semua HPP
</a> -->

</div>
@endsection
