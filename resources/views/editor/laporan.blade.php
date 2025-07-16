@extends('masterlayout')

@section('content')
<div class="container mt-3">


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif


<form method="GET" action="{{ route('editor.laporan') }}" class="mb-3">
    <div class="row">
        {{-- Filter Tanggal --}}
        <div class="col-md-6 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{ request('daterange') }}">
        </div>

        {{-- Filter Nama Editor --}}
        <div class="col-md-6 mb-2">
            <select name="editor" class="form-control">
                <option value="">Semua Editor</option>
                @foreach($semuaEditor as $editor)
                    <option value="{{ $editor->id }}" {{ request('editor') == $editor->id ? 'selected' : '' }}>
                        {{ $editor->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Status --}}
        <div class="col-md-6 mb-2">
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="onproses" {{ request('status') == 'onproses' ? 'selected' : '' }}>On Proses</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        {{-- Search Nama Pembeli --}}
        <div class="col-md-6 mb-2">
            <input type="text" name="nama_pembeli" class="form-control"
                   placeholder="Cari Nama Pembeli"
                   value="{{ request('nama_pembeli') }}">
        </div>

        <div class="col-md-1 ">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-1">
            <a href="{{ route('editor.laporan') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>



    @if($jobdesk->isEmpty())
        <div class="alert alert-info">Belum ada data jobdesk.</div>
    @else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-box mr-1" style="color: #31beb4;"></i>Daftar Jobdesk Editor</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
            <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Editor</th>
                        <th>Tanggal Ambil Jobdesk</th>
                        <th>Status</th>
                        <th>Nama Pembeli</th>
                        <th>Metode Pengiriman</th>
                        <th>Detail</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach($jobdesk as $jd)
                        @foreach($jd->penjualan->detailPenjualan as $index => $dp)
                        <tr>
                            @if($index === 0)
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $jd->user->name }}</td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $jd->created_at->format('d M Y H:i') }}</td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">
                                    @if($jd->status == 'selesai')
                                        <span class="badge badge-success">Selesai</span>
                                    @else
                                        <span class="badge badge-warning">On Proses</span>
                                    @endif
                                </td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $jd->penjualan->nama_pembeli }}</td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $jd->penjualan->metode_pengiriman }}</td>
                                <td rowspan="{{ $jd->penjualan->detailPenjualan->count() }}">{{ $jd->penjualan->detail }}</td>
                            @endif
                            <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
       </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
    {{ $jobdesk->links('pagination::bootstrap-4') }}
</div>

    @endif
</div>
@endsection
