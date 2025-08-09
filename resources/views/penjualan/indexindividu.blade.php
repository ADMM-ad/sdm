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
<form method="GET" class="mb-3">
    <div class="row">
        <div class="col-md-6 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{ request('daterange') }}">
        </div>
        <div class="col-md-6 mb-2">
    <select name="platform" id="platform" class="form-control">
        <option value="">Semua Platform</option>
        <option value="nonshopee" {{ request('platform') == 'nonshopee' ? 'selected' : '' }}>Non Shopee</option>
        <option value="shopee" {{ request('platform') == 'shopee' ? 'selected' : '' }}>Shopee</option>
    </select>
</div>
<div class="col-md-6 mb-2">
    <input type="text" name="nama_pembeli" class="form-control"
           placeholder="Cari Nama Pembeli"
           value="{{ request('nama_pembeli') }}">
</div>

        <div class="col-md-6 col-12">
            <div class="row">
                <div class="col-6">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-6">
                    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </div>
        </div>
    </div>
</form>



    @if($penjualan->isEmpty())
        <div class="alert alert-info">Belum ada data penjualan.</div>
    @else
  <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-invoice mr-1" style="color: #00518d;"></i>Laporan Closing Penjualan Anda</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
<thead>
    <tr>
        <th>No</th> <!-- Ubah dari ID ke No -->
        <th>Tanggal</th>
      
        <th>No Pesanan</th>
        <th>Nama Pembeli</th>
        <th>Alamat</th>
        <th>No HP</th>
        <th>Kode Pos</th>
        <th>Metode Pengiriman</th>
        <th>Metode Pembayaran</th>
        <th>No Resi</th>
        <th>Status Pesanan</th>
        <th>Nama Produk</th>
        <th>Jumlah</th>
        <th>Total Harga</th>
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
            <td>{{ $no++ }}</td> <!-- Ganti ID dengan nomor urut -->
            <td>{{ $p->tanggal }}</td>
          
            <td>{{ $p->nama_pembeli }}</td>
            <td>{{ $p->order_id }}</td>
            <td>{{ $p->alamat }} ({{ $p->kodepos }})</td>
            <td>{{ $p->no_hp }}</td>
            <td>{{$p->kode_pos}}</td>
            <td>{{ strtoupper($p->metode_pengiriman) }}</td>
            <td>{{ strtoupper($p->metode_pembayaran) }}</td>
            <td>{{$p->no_resi}}</td>
              <td>{{$p->status_pesanan}}</td>
            <td colspan="3" class="text-center text-muted">Belum ada produk</td>
            
        </tr>
    @else
        @foreach($p->detailPenjualan as $index => $dp)
            <tr>
                @if($index === 0)
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td> <!-- No Urut -->
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->tanggal }}</td>
       
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->order_id }}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->alamat }} ({{ $p->kodepos }})</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_hp }}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->kodepos}}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ strtoupper($p->metode_pengiriman) }}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ strtoupper($p->metode_pembayaran) }}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi}}</td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pesanan}}</td>
                @endif
                <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                <td class="px-3 py-2">Rp{{ number_format($dp->total_harga, 0, ',', '.') }}</td>
                @if($index === 0)
                <td rowspan="{{ $p->detailPenjualan->count() }}">
                        Rp{{ number_format($p->ongkir, 0, ',', '.') }}
                    </td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">
                        <strong>Rp{{ number_format($p->total_bayar, 0, ',', '.') }}</strong>
                    </td>
                    <td rowspan="{{ $p->detailPenjualan->count() }}">
                        @if(empty($p->no_resi))
  <a href="{{ route('penjualan.edit', $p->id) }}?{{ http_build_query(request()->only( 'daterange', 'platform',  'nama_pembeli', )) }}"
   class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>
@endif
                  <a href="{{ route('penjualan.detail', $p->id) }}?{{ http_build_query(request()->only( 'daterange', 'platform',  'nama_pembeli', )) }}"
         class="btn btn-info btn-sm">
    <i class="fas fa-eye"></i> 
</a>


                  @php
    $queryString = http_build_query(request()->only( 'daterange', 'platform', 'nama_pembeli'));
@endphp

<button type="button"
    class="btn btn-danger btn-sm"
    onclick="confirmDelete('{{ route('penjualanindv.destroy', $p->id) }}?{{ $queryString }}')">
    <i class="fas fa-trash-alt"></i> 
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
