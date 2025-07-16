@extends('masterlayout')

@section('content')
<div class="container mt-3">

    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title"><i class="fas fa-file-upload mr-1"></i> Import Data Iklan</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <form action="{{ route('iklan.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="form-group">
                    <label for="file">Upload File Excel (CSV/XLS/XLSX)</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Import</button>
            </form>

            @if($iklans->count())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-1" style="color: #31beb4;"></i>Laporan Iklan</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Awal Pelaporan</th>
                            <th>Nama CS</th>
                            <th>Nama Kampanye</th>
                            <th>Hasil</th>
                            <th>Jumlah yang Dibelanjakan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($iklans as $index => $iklan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($iklan->awal_pelaporan)->format('d-m-Y') }}</td>
                                <td>{{ $iklan->kampanye->user->name ?? '-' }}</td>
                                <td>{{ $iklan->kampanye->kode_kampanye ?? '-' }}</td>
                                <td>{{ $iklan->hasil }}</td>
                                <td>Rp {{ number_format($iklan->jumlah_dibelanjakan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
              </div>
                </div>
                  </div>
            @else
                <p class="text-muted">Belum ada data iklan yang diimpor.</p>
            @endif

        </div>
    </div>

</div>
@endsection
