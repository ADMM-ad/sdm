@extends('masterlayout')

@section('content')
<div class="container">

    <div class="card card-primary card-outline mt-3 " style="border-color: #00518d;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"> <i class="fas fa-file-invoice" style="color: #00518d;"></i> Detail Penjualan </h4>
            
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>Tanggal:</strong> {{ $penjualan->tanggal }}</p>
                    <p><strong>No Pesanan:</strong> {{ $penjualan->order_id }}</p>
                    <p><strong>No Resi:</strong> {{ $penjualan->no_resi }}</p>
                    <p><strong>Ekspedisi:</strong> {{ $penjualan->kurir }}</p>
                    <p><strong>Metode Pengiriman:</strong> {{ $penjualan->metode_pengiriman }}</p>
                    <p><strong>Metode Pembayaran:</strong> {{ $penjualan->metode_pembayaran }}</p>
                    <p><strong>Ongkir:</strong> Rp{{ number_format($penjualan->ongkir, 0, ',', '.') }}</p>
                    <p><strong>Total Bayar:</strong> Rp{{ number_format($penjualan->total_bayar, 0, ',', '.') }}</p>
                    <p><strong>DP :</strong> Rp{{ number_format($penjualan->dp, 0, ',', '.') }}</p>
                    <p><strong>Status Pesanan:</strong> {{ $penjualan->status_pesanan }}</p>
                         @if($penjualan->status_pembatalan)
    <p><strong>Status Pesanan:</strong> {{ $penjualan->status_pembatalaan }}</p>
@endif
                     @if($penjualan->alasan_batal)
    <p><strong>Alasan Batal:</strong> {{ $penjualan->alasan_batal }}</p>
@endif
                 @if($penjualan->bukti)
    <strong>Bukti Pembayaran:</strong><br>
    <img src="{{ asset($penjualan->bukti) }}" alt="Bukti" class="img-fluid" style="max-width: 300px;">
@endif

                </div>
                <div class="col-md-6">
                    <p><strong>Nama Pembeli:</strong> {{ $penjualan->nama_pembeli }}</p>
                    <p><strong>No HP:</strong> {{ $penjualan->no_hp }}</p>
                    <p><strong>Alamat:</strong> {{ $penjualan->alamat }}</p>
                    <p><strong>Kode Pos:</strong> {{ $penjualan->kodepos }}</p>
                    <p><strong>Wilayah:</strong> {{ $penjualan->wilayah }}, {{ $penjualan->kecamatan }}, {{ $penjualan->kota }}, {{ $penjualan->provinsi }}</p>
                    <p><strong>Catatan:</strong>{{ $penjualan->catatan }}</p>
                    <p><strong>Detail:</strong>{{ $penjualan->detail }}</p>
                </div>
                </div>
            </div>

           

            

          

        
           <div class="row">
    <div class="col-12">
        <div class="card">
            
            <div class="card-body table-responsive p-0">
              <table class="table table-hover table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Detail Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->detailPenjualan as $item)
                    <tr>
                        <td>{{ $item->produk->nama_produk }}</td>
                        <td>{{ $item->produk->detail_produk }}</td>
                        <td>{{ $item->jumlah }}</td>
                        <td>Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        
        </div>
        
        </div>
        
        </div>
  

    </div>
 
        

@php
    $queryParams = request()->query();
@endphp

@if(auth()->user()->role === 'admin')
    <a href="{{ route('penjualan.laporan', $queryParams) }}" class="btn btn-secondary mb-3">
        <i class="fas fa-reply"></i> Kembali
    </a>
@else
    <a href="{{ route('penjualan.index', $queryParams) }}" class="btn btn-secondary mb-3">
        <i class="fas fa-reply"></i> Kembali
    </a>
@endif
    
</div>
@endsection
