@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F; border-color: #1DCD9F;">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i>Edit Produk</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nama Produk -->
                <div class="form-group">
                    <label for="nama_produk"><i class="fas fa-box mr-1" style="color: #1DCD9F;"></i>Nama Produk</label>
                    <input type="text"
                        class="form-control @error('nama_produk') is-invalid @enderror"
                        id="nama_produk"
                        name="nama_produk"
                        value="{{ $produk->nama_produk }}"
                        required>
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
        rows="3">{{ old('detail_produk', $produk->detail_produk) }}</textarea>
    @error('detail_produk')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

                <!-- HPP -->
                <div class="form-group">
                    <label for="hpp"><i class="fas fa-money-bill-wave mr-1" style="color: #1DCD9F;"></i>HPP</label>
                    <input type="number" step="0.01"
                        class="form-control @error('hpp') is-invalid @enderror"
                        id="hpp"
                        name="hpp"
                        value="{{ $produk->hpp }}"
                        required>
                    @error('hpp')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Harga Jual -->
                <div class="form-group">
                    <label for="harga_jual"><i class="fas fa-tag mr-1" style="color: #1DCD9F;"></i>Harga Jual</label>
                    <input type="number" step="0.01"
                        class="form-control @error('harga_jual') is-invalid @enderror"
                        id="harga_jual"
                        name="harga_jual"
                        value="{{ $produk->harga_jual }}"
                        required>
                    @error('harga_jual')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

<!-- Jenis Produk -->
<div class="form-group">
    <label for="jenis_produk"><i class="fas fa-tags mr-1" style="color: #1DCD9F;"></i>Jenis Produk</label>
    <select class="form-control @error('jenis_produk') is-invalid @enderror" id="jenis_produk" name="jenis_produk" required>
        <option value="" disabled {{ is_null($produk->jenis_produk) ? 'selected' : '' }}>Pilih jenis produk</option>
        <option value="stiker" {{ $produk->jenis_produk == 'stiker' ? 'selected' : '' }}>Stiker</option>
        <option value="non_stiker" {{ $produk->jenis_produk == 'non_stiker' ? 'selected' : '' }}>Non Stiker</option>
    </select>
    @error('jenis_produk')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>


                <button type="submit" class="btn btn-success mt-3">Perbarui</button>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
