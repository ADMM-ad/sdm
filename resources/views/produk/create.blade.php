@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1" style="color: #00518d;"></i>Tambah Produk Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf

                <!-- Nama Produk -->
                <div class="form-group">
                    <label for="nama_produk"><i class="fas fa-box mr-1" style="color: #00518d;"></i>Nama Produk</label>
                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                        id="nama_produk" name="nama_produk"
                        placeholder="Masukkan Nama Produk" required
                        value="{{ old('nama_produk') }}">
                    @error('nama_produk')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
<!-- Detail Produk -->
<div class="form-group">
    <label for="detail_produk"><i class="fas fa-align-left mr-1" style="color: #00518d;"></i>Detail Produk</label>
    <textarea
        class="form-control @error('detail_produk') is-invalid @enderror"
        id="detail_produk"
        name="detail_produk"
        rows="3"
        placeholder="Masukkan Detail Dan Deskripsi Produk">{{ old('detail_produk') }}</textarea>
    @error('detail_produk')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

                <!-- HPP -->
                <div class="form-group">
                    <label for="hpp"><i class="fas fa-money-bill-wave mr-1" style="color: #00518d;"></i>HPP</label>
                    <input type="number" step="0.01" class="form-control @error('hpp') is-invalid @enderror"
                        id="hpp" name="hpp"
                        placeholder="Masukkan Harga Pokok Penjualan" required
                        value="{{ old('hpp') }}">
                    @error('hpp')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div class="form-group">
                    <label for="harga_jual"><i class="fas fa-dollar-sign mr-1" style="color: #00518d;"></i>Harga Jual</label>
                    <input type="number" step="0.01" class="form-control @error('harga_jual') is-invalid @enderror"
                        id="harga_jual" name="harga_jual"
                        placeholder="Masukkan Harga Jual Produk" required
                        value="{{ old('harga_jual') }}">
                    @error('harga_jual')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

   <!-- Jenis Produk -->
<div class="form-group">
    <label for="jenis_produk_id"><i class="fas fa-tag mr-1" style="color: #00518d;"></i>Kategori Produk</label>
    <select class="form-control @error('jenis_produk_id') is-invalid @enderror"
            id="jenis_produk_id" name="jenis_produk_id" required>
        <option value="" disabled selected>Pilih Kategori Produk</option>
        @foreach($jenisprodukList as $jp)
            <option value="{{ $jp->id }}" {{ old('jenis_produk_id') == $jp->id ? 'selected' : '' }}>
                {{ $jp->kategori }}
            </option>
        @endforeach
    </select>
    @error('jenis_produk_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>



<!-- Jenis Lead (optional) -->
<div class="form-group">
    <label for="jenis_lead_id"><i class="fas fa-random mr-1" style="color: #00518d;"></i>Jenis Lead </label>
    <select class="form-control @error('jenis_lead_id') is-invalid @enderror"
            name="jenis_lead_id" id="jenis_lead_id">
        <option value="">Pilih Jenis Lead</option>
        @foreach($jenisleadList as $lead)
            <option value="{{ $lead->id }}" {{ old('jenis_lead_id') == $lead->id ? 'selected' : '' }}>
                {{ $lead->jenis }}
            </option>
        @endforeach
    </select>
    @error('jenis_lead_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>


                <button type="submit" class="btn btn-success mt-3">  <i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary mt-3">  <i class="fas fa-reply me-1"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
