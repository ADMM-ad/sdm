@extends('masterlayout')

@section('content')
<div class="container mt-3">
      <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1" style="color: #00518d;"></i>Tambah Kategori Produk</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('jenisproduk.store') }}" method="POST">
                @csrf

                <!-- Input Kategori -->
                <div class="form-group">
                    <label for="kategori"><i class="fas fa-tag mr-1" style="color: #00518d;"></i>Kategori</label>
                    <input type="text"
                           class="form-control @error('kategori') is-invalid @enderror"
                           id="kategori" name="kategori"
                           placeholder="Masukkan Kategori Produk" required
                           value="{{ old('kategori') }}">
                    @error('kategori')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('jenisproduk.index') }}" class="btn btn-secondary mt-3"> <i class="fas fa-reply me-1"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
