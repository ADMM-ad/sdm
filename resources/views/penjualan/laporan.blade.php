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
   <form method="GET" action="{{ route('penjualan.laporan') }}" class="mb-3">
    <div class="row">
        {{-- Baris Pertama: Dropdown CS dan Tanggal --}}
        <div class="col-md-6 mb-2">
            <select name="cs" id="cs" class="form-control">
                <option value="">Semua Customer Service</option>
                @foreach($semuaUser as $user)
                    <option value="{{ $user->id }}" {{ request('cs') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
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
    <select name="jenis_produk" id="jenis_produk" class="form-control">
        <option value="">Semua Jenis Produk</option>
        <option value="stiker" {{ request('jenis_produk') == 'stiker' ? 'selected' : '' }}>Stiker</option>
        <option value="non_stiker" {{ request('jenis_produk') == 'non_stiker' ? 'selected' : '' }}>Non Stiker</option>
    </select>
</div>
<div class="col-md-6 mb-2">
    <input type="text" name="nama_pembeli" class="form-control" placeholder="Cari Nama Pembeli" value="{{ request('nama_pembeli') }}">
</div>

<div class="col-md-6 mb-2">
    <select name="waktu_input" class="form-control">
        <option value="">Semua Waktu Input</option>
        <option value="pagi" {{ request('waktu_input') == 'pagi' ? 'selected' : '' }}>Sebelum Jam 11</option>
        <option value="siang" {{ request('waktu_input') == 'siang' ? 'selected' : '' }}>Setelah Jam 11</option>
    </select>
</div>



        {{-- Baris Kedua: Semua Button tampil dalam 1 baris penuh (5 kolom rata) --}}
        <div class="col-12">
            <div class="row">
                <div class="col-md-2 col-6 mb-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <a href="{{ route('penjualan.laporan') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <a href="{{ route('penjualan.export', request()->query()) }}" class="btn btn-success w-100">Export</a>
                </div>
                <div class="col-md-2 col-6 mb-2">
                <a href="{{ route('penjualan.resiexcel', request()->query()) }}" class="btn btn-info w-100">Create Resi</a>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <button type="button" class="btn text-white w-100" style="background-color: #f1582b;" data-toggle="modal" data-target="#uploadModal">Shopee</button>
                </div>
                <div class="col-md-2 col-6 mb-2">
                    <button type="button" class="btn btn-dark w-100" data-toggle="modal" data-target="#uploadModalMengantar">Mengantar</button>
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
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice mr-1" style="color: #1DCD9F;"></i>Laporan Closing Penjualan
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
                                    <th>No Pesanan</th>
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
                                    <th>Cashback</th>
                                    <th>Biaya COD</th>
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
                                            <td>{{ $p->user->order_id }}</td>
                                            <td>{{ $p->nama_pembeli }} </td>
                                            <td>{{ $p->alamat }} ({{ $p->kodepos }})</td>
                                            <td>{{ $p->no_hp }}</td>
                                            <td>{{ $p->kodepos }}</td>
                                            <td>{{ strtoupper($p->metode_pengiriman) }}</td>
                                            <td>{{ $p->metode_pembayaran }}</td>
                                            <td>{{ $p->kurir }}</td>
                                            <td>{{$p->no_resi}}</td>
                                            <td>{{$p->status_pesanan}}</td>
                                            
                                            <td colspan="3" class="text-center text-muted">Belum ada produk</td>
                                        </tr>
                                    @else
                                        @foreach($p->detailPenjualan as $index => $dp)
                                            <tr>
                                                @if($index === 0)
                                                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                                    <td rowspan="{{ $p->detailPenjualan->count() }}">
                                                        {{ $p->tanggal }}
                                                    </td>
                                                    <td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}
</td>
                                                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
                                                    <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->order_id }}</td>
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
                                                <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                                                @if($index === 0)
                                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->detail}}</td>
                                                @endif
                                                <td class="px-3 py-2">{{ $dp->nama_variasi ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                                                <td class="px-3 py-2">Rp{{ number_format($dp->total_harga, 0, ',', '.') }}</td>
                                                
                                                @if($index === 0)
                                                 <td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ 'Rp ' . number_format($p->total_hpp, 0, ',', '.') }}
</td>
<td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ 'Rp ' . number_format($p->ongkir, 0, ',', '.') }}
</td>
<td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ 'Rp ' . number_format($p->cashback, 0, ',', '.') }}
</td>
<td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ 'Rp ' . number_format($p->biaya_cod, 0, ',', '.') }}
</td>

            <td rowspan="{{ $p->detailPenjualan->count() }}">
                <strong>Rp{{ number_format($p->total_bayar, 0, ',', '.') }}</strong>
            </td>

            <td rowspan="{{ $p->detailPenjualan->count() }}">
               <a href="{{ route('penjualan.edit', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}"
   class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

         <a href="{{ route('penjualan.detail', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}"
         class="btn btn-info btn-sm">
    <i class="fas fa-eye"></i> 
</a>

        
             @php
    $queryString = http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input'));
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

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('upload.shopee') }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel">Upload Data Penjualan Shopee</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="file">Pilih File (CSV/XLSX)</label>
          <input type="file" name="file" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </form>
  </div>
</div>


<!-- Modal Upload Mengantar -->
<div class="modal fade" id="uploadModalMengantar" tabindex="-1" aria-labelledby="uploadModalMengantarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('upload.mengantar') }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalMengantarLabel">Upload Data Pengiriman Mengantar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="file">Pilih File (CSV/XLSX)</label>
          <input type="file" name="file" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-dark">Upload</button>
      </div>
    </form>
  </div>
</div>

@endsection
