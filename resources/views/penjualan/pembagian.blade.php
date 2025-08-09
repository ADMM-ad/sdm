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
    <form method="GET" action="{{ route('penjualan.pembagian') }}" class="mb-3">
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
                    placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}">
            </div>
            <div class="col-md-6 mb-2">
                <select name="platform" id="platform" class="form-control">
                    <option value="">Semua Platform</option>
                    <option value="nonshopee" {{ request('platform') == 'nonshopee' ? 'selected' : '' }}>Non Shopee
                    </option>
                    <option value="shopee" {{ request('platform') == 'shopee' ? 'selected' : '' }}>Shopee</option>
                </select>
            </div>

        <div class="col-md-6 mb-2">
    <select name="jenis_produk" id="jenis_produk" class="form-control">
        <option value="">Semua Jenis Produk</option>
        {{-- Loop untuk menampilkan semua jenis produk dari database --}}
        @foreach($jenisProdukOptions as $jenis) 
            <option value="{{ $jenis->id }}" {{ request('jenis_produk') == $jenis->id ? 'selected' : '' }}>
                {{ $jenis->kategori }} 
            </option>
        @endforeach 
    </select>
</div>

            <div class="col-md-6 mb-2">
                <input type="text" name="nama_pembeli" class="form-control" placeholder="Cari Nama Pembeli"
                    value="{{ request('nama_pembeli') }}">
            </div>

            <div class="col-md-6 mb-2">
                <select name="waktu_input" class="form-control">
                    <option value="">Semua Waktu Input</option>
                    <option value="pagi" {{ request('waktu_input') == 'pagi' ? 'selected' : '' }}>Sebelum Jam 11
                    </option>
                    <option value="siang" {{ request('waktu_input') == 'siang' ? 'selected' : '' }}>Setelah Jam 11
                    </option>
                </select>
            </div>

            {{-- Baris Kedua: Semua Button tampil dalam 1 baris penuh (5 kolom rata) --}}
            <div class="col-12">
                <div class="row">
                    <div class="col-md-2 col-6 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('penjualan.pembagian') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                <a href="{{ route('penjualan.export.pembagian', request()->all()) }}" class="btn btn-success mb-3">
    <i class="fas fa-file-excel"></i> Export 
</a>

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
                        <i class="fas fa-file-invoice mr-1" style="color: #00518d;"></i>Laporan Pembagian Keuangan
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead>
                            <tr>
                             
                                <th>No</th>
                                <th>Tanggal</th>
                             
                                <th>Nama CS</th>
                              
                                <th>Nama Pembeli</th>
                            
                           
                  
                                <th>Metode Pengiriman</th>
                             
                           
                            
                                <th>Status Pesanan</th>

                                <th>Produk</th>
                                <th>Detail</th>
                                <th>Variasi</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Total HPP</th>
                                <th>Pembagian HPP</th>
                                <th>Total Omset</th>
                                <th>Pembagian Omset</th>
                                <th>Total Ongkir</th>
                                <th>Pembagian Ongkir</th>
                                <th>Total Biaya COD </th>
                                <th>Pembagian Biaya COD</th>
                                <th>Total Cashback</th>
                                <th>Pembagian Cashback</th>
                                <th>Biaya Iklan Per Transaksi</th> {{-- Kolom baru ditambahkan di sini --}}
                                <th>Profit</th>
    
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $penjualan->firstItem(); @endphp {{-- Inisialisasi nomor dengan nomor item pertama dari paginasi --}}
                            @foreach($penjualan as $p)
                            @if($p->detailPenjualan->isEmpty())
                            <tr>
                               
                                <td>{{ $no++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d') }}</td> {{-- Format tanggal --}}
                               
                                <td>{{ $p->user->name ?? '-' }}</td>
                              
                                <td>{{ $p->nama_pembeli }} </td>
                              
                                <td>{{ strtoupper($p->metode_pengiriman) }}</td>
                                
                                <td>{{$p->status_pesanan}}</td>
                               

                                <td colspan="4" class="text-center text-muted">Belum ada produk</td>
                                <td>{{ 'Rp ' . number_format($p->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->total_hpp ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->total_hpp ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->total_bayar ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->hasil_pembagian_omset ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->ongkir ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->hasil_pembagian_ongkir ?? 0, 0, ',', '.') }}</td>
                                 <td>{{ 'Rp ' . number_format($p->biaya_cod ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->hasil_pembagian_biayacod ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->cashback ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->hasil_pembagian_cashback ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->biayaIklanPerTransaksi ?? 0, 0, ',', '.') }}</td> {{-- Menampilkan biaya iklan per transaksi --}}
                                <td>{{'Rp ' . number_format($p->profit ?? 0, 0, ',', '.') }}</td>
                               
                                <td>
                                                                    <a href="{{ route('penjualan.edit', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}&from={{ urlencode(request()->fullUrl()) }}"
   class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>

                                    <a href="{{ route('penjualan.detail', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @php
                                    $queryString = http_build_query(request()->only('cs', 'daterange',
                                    'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input'));
                                    @endphp

                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="confirmDelete('{{ route('penjualanindv.destroy', $p->id) }}?{{ $queryString }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            @else
                            @foreach($p->detailPenjualan as $index => $dp)
                            <tr>
                                @if($index === 0)
                                {{-- Checkbox untuk setiap grup baris --}}
                           
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d-H:i') }} {{-- Format tanggal --}}
                                </td>
                               
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
              
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                              
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ strtoupper($p->metode_pengiriman)}}</td>
                            
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
                                    {{ 'Rp ' . number_format($p->total_hpp ?? 0, 0, ',', '.') }}
                                </td>
                                @endif

                                <td class="px-3 py-2">
  
    @php
        $hpp = 0;
        if ($dp->penjualan->total_hpp != 0) {
            $hpp = ($dp->produk->hpp ?? 0) * $dp->jumlah;
        }
    @endphp
    Rp{{ number_format($hpp, 0, ',', '.') }}

</td>
 @if($index === 0)
                                 <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->total_bayar ?? 0, 0, ',', '.') }}
                                </td>
                                 @endif

@php
    $hasilOmset = $dp->hasil_pembagian_omset ?? 0;
    $hasilOngkir = $dp->hasil_pembagian_ongkir ?? 0;
    $total = $hasilOmset + $hasilOngkir;
@endphp

<td class="px-3 py-2">
    Rp{{ number_format($total, 0, ',', '.') }}
</td>

@if($index === 0)
<td rowspan="{{ $p->detailPenjualan->count() }}">
 {{ 'Rp ' . number_format($p->ongkir ?? 0, 0, ',', '.') }}
</td>
 @endif

<td class="px-3 py-2">{{ 'Rp ' . number_format($dp->hasil_pembagian_ongkir ?? 0, 0, ',', '.') }}</td>

@if($index === 0)
 <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->biaya_cod ?? 0, 0, ',', '.') }}
                                </td>
@endif
<td class="px-3 py-2">{{ 'Rp ' . number_format($dp->hasil_pembagian_biayacod ?? 0, 0, ',', '.') }}</td>


@if($index === 0)
<td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->cashback ?? 0, 0, ',', '.') }}
                                </td>
@endif

<td class="px-3 py-2">{{ 'Rp ' . number_format($dp->hasil_pembagian_cashback ?? 0, 0, ',', '.') }}</td>

                                @if($index === 0)
                                
                                
                               
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->biayaIklanPerTransaksi ?? 0, 0, ',', '.') }}
                                </td> {{-- Menampilkan biaya iklan per transaksi --}}
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{'Rp ' . number_format($p->profit ?? 0, 0, ',', '.') }}
                                </td>

                              

                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                   <a href="{{ route('penjualan.edit', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}&from={{ urlencode(request()->fullUrl()) }}"
   class="btn btn-warning btn-sm">
    <i class="fas fa-edit"></i>
</a>


                                    <a href="{{ route('penjualan.detail', $p->id) }}?{{ http_build_query(request()->only('cs', 'daterange', 'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input')) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>


                                    @php
                                    $queryString = http_build_query(request()->only('cs', 'daterange',
                                    'platform', 'jenis_produk', 'nama_pembeli', 'waktu_input'));
                                    @endphp

                                    <button type="button" class="btn btn-danger btn-sm"
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


<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
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
