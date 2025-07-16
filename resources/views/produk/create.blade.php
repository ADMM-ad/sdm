@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F; border-color: #1DCD9F;">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i>Tambah Produk Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('produk.store') }}" method="POST">
                @csrf

                <!-- Nama Produk -->
                <div class="form-group">
                    <label for="nama_produk"><i class="fas fa-box mr-1" style="color: #1DCD9F;"></i>Nama Produk</label>
                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                        id="nama_produk" name="nama_produk"
                        placeholder="Masukkan nama produk" required
                        value="{{ old('nama_produk') }}">
                    @error('nama_produk')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
<!-- Detail Produk -->
<div class="form-group">
    <label for="detail_produk"><i class="fas fa-align-left mr-1" style="color: #1DCD9F;"></i>Detail Produk</label>
    <textarea
        class="form-control @error('detail_produk') is-invalid @enderror"
        id="detail_produk"
        name="detail_produk"
        rows="3"
        placeholder="Masukkan detail atau deskripsi produk">{{ old('detail_produk') }}</textarea>
    @error('detail_produk')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

                <!-- HPP -->
                <div class="form-group">
                    <label for="hpp"><i class="fas fa-money-bill-wave mr-1" style="color: #1DCD9F;"></i>HPP</label>
                    <input type="number" step="0.01" class="form-control @error('hpp') is-invalid @enderror"
                        id="hpp" name="hpp"
                        placeholder="Masukkan harga pokok penjualan (HPP)" required
                        value="{{ old('hpp') }}">
                    @error('hpp')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div class="form-group">
                    <label for="harga_jual"><i class="fas fa-tag mr-1" style="color: #1DCD9F;"></i>Harga Jual</label>
                    <input type="number" step="0.01" class="form-control @error('harga_jual') is-invalid @enderror"
                        id="harga_jual" name="harga_jual"
                        placeholder="Masukkan harga jual" required
                        value="{{ old('harga_jual') }}">
                    @error('harga_jual')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
    <label for="jenis_produk"><i class="fas fa-tags mr-1" style="color: #1DCD9F;"></i>Jenis Produk</label>
    <select class="form-control @error('jenis_produk') is-invalid @enderror" id="jenis_produk" name="jenis_produk" required>
        <option value="" disabled selected>Pilih jenis produk</option>
        <option value="stiker" {{ old('jenis_produk') == 'stiker' ? 'selected' : '' }}>Stiker</option>
        <option value="non_stiker" {{ old('jenis_produk') == 'non_stiker' ? 'selected' : '' }}>Non Stiker</option>
    </select>
    @error('jenis_produk')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

                <button type="submit" class="btn btn-success mt-3">Simpan</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
