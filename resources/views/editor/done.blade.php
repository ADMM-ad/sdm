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

@if($jobdesks->isEmpty())
    <div class="alert alert-info">
        Belum ada jobdesk yang selesai.
    </div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check mr-1" style="color: green;"></i> Jobdesk Selesai</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Metode Pengiriman</th>
                            <th>Nama Pembeli</th>
                            <th>No Resi</th>
                            <th>Detail</th>
                            <th>Produk</th>
                            <th>Variasi</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($jobdesks as $jd)
                            @php $p = $jd->penjualan; @endphp
                            @foreach($p->detailPenjualan as $index => $dp)
                                <tr>
                                    @if($index === 0)
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->metode_pengiriman }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi ?? '-' }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->detail }}</td>
                                    @endif
                                    <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $dp->nama_variasi ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                                    @if($index === 0)
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">
                                            <span class="badge badge-success">Selesai</span>
                                        </td>
                                    @endif
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
    {{ $jobdesks->links('pagination::bootstrap-4') }}
</div>

@endif
</div>
@endsection
