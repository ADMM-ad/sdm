@extends('masterlayout')

@section('content')
<div class="container mt-3">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="fas fa-check-circle mr-2"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span>&times;</span>
    </button>
</div>
@endif


{{-- Filter Tanggal --}}
<form method="GET" action="{{ route('penjualan.cekshopee') }}" class="mb-3">
    <div class="row">
        <div class="col-md-4 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}">
        </div>
        <div class="col-md-2 mb-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2 mb-2">
            <a href="{{ route('penjualan.cekshopee') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>

@if($penjualan->isEmpty())
    <div class="alert alert-info">Tidak ada data penjualan Shopee yang harus di cek.</div>
@else
 <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-shopping-bag mr-1" style="color: #1DCD9F;"></i>Pilihan Penjualan Shopee
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-bordered text-nowrap">
                            <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam Input</th>
                            <th>Nama CS</th>
                            <th>Nama Pembeli</th>
                            <th>Alamat</th>
                            <th>No HP</th>
                            <th>Kode Pos</th>
                            <th>Metode Pengiriman</th>
                            <th>Metode Pembayaran</th>
                            <th>Ekspedisi</th>
                            <th>No Resi</th>
                            <th>Status Pesanan</th>
                            <th>Produk</th>
                            <th>Detail</th>
                            <th>Variasi</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Total HPP</th>
                            <th>Ongkir</th>
                            <th>Total Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($penjualan as $p)
                            @if($p->detailPenjualan->isEmpty())
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $p->tanggal }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                                    <td>{{ $p->user->name ?? '-' }}</td>
                                    <td>{{ $p->nama_pembeli }}</td>
                                    <td>{{ $p->alamat }}</td>
                                    <td>{{ $p->no_hp }}</td>
                                    <td>{{ $p->kodepos }}</td>
                                    <td>{{ strtoupper($p->metode_pengiriman) }}</td>
                                    <td>{{ $p->metode_pembayaran }}</td>
                                    <td>{{ $p->kurir }}</td>
                                    <td>{{ $p->no_resi }}</td>
                                    <td>{{ $p->status_pesanan }}</td>
                                    <td colspan="3" class="text-center text-muted">Belum ada produk</td>
                                    <td>-</td><td>-</td>
                                    <td>{{ 'Rp ' . number_format($p->total_hpp, 0, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($p->ongkir, 0, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($p->total_bayar, 0, ',', '.') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm btn-confirm" data-id="{{ $p->id }}" data-toggle="modal" data-target="#confirmModal">
                                            <i class="fas fa-user-check"></i> Punya Saya
                                        </button>
                                    </td>
                                </tr>
                            @else
                                @foreach($p->detailPenjualan as $index => $dp)
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->tanggal }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->alamat }} ({{ $p->kodepos }})</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_hp }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->kodepos }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ strtoupper($p->metode_pengiriman) }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->metode_pembayaran}}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->kurir}}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi}}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pesanan}}</td>
                                        @endif
                                        <td>{{ $dp->produk->nama_produk ?? '-' }}</td>
                                        @if($index === 0)
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->detail }}</td>
                                        @endif
                                        <td>{{ $dp->nama_variasi ?? '-' }}</td>
                                        <td>{{ $dp->jumlah }}</td>
                                        <td>Rp{{ number_format($dp->total_harga, 0, ',', '.') }}</td>
                                        @if($index === 0)
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ 'Rp ' . number_format($p->total_hpp, 0, ',', '.') }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">{{ 'Rp ' . number_format($p->ongkir, 0, ',', '.') }}</td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}"><strong>Rp{{ number_format($p->total_bayar, 0, ',', '.') }}</strong></td>
                                            <td rowspan="{{ $p->detailPenjualan->count() }}">
                                                <button type="button" class="btn btn-success btn-sm btn-confirm" data-id="{{ $p->id }}" data-toggle="modal" data-target="#confirmModal">
    <i class="fas fa-user-check"></i> Punya Saya
</button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<div class="d-flex justify-content-end mt-2">
    {{ $penjualan->links('pagination::bootstrap-4') }}
</div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="confirmForm" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penugasan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin mengambil penjualan ini sebagai milik Anda?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
            <button type="submit" class="btn btn-success">Ya, Ambil</button>
          </div>
        </div>
    </form>
  </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn-confirm');
        const form = document.getElementById('confirmForm');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                form.action = `/penjualan/ambil/${id}`;
            });
        });
    });
</script>

@endsection
