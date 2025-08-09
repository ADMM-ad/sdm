@extends('masterlayout')

@section('content')
<div class="container mt-3">
       <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1" style="color: #00518d;"></i>Edit Kategori Produk</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('jenisproduk.update', $jenisproduk->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Input Kategori -->
                <div class="form-group">
                    <label for="kategori"><i class="fas fa-tag mr-1" style="color: #00518d;"></i>Kategori</label>
                    <input type="text"
                           class="form-control @error('kategori') is-invalid @enderror"
                           id="kategori"
                           name="kategori"
                           value="{{ old('kategori', $jenisproduk->kategori) }}"
                           required>
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
