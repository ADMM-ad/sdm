@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1" style="color: #00518d;"></i>Tambah Jenis Lead</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('jenislead.store') }}" method="POST">
                @csrf

                <!-- Input Jenis Lead -->
                <div class="form-group">
                    <label for="jenis"><i class="fas fa-random mr-1" style="color: #00518d;"></i>Jenis Lead</label>
                    <input type="text"
                           class="form-control @error('jenis') is-invalid @enderror"
                           id="jenis" name="jenis"
                           placeholder="Masukkan Jenis Lead" required
                           value="{{ old('jenis') }}">
                    @error('jenis')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('jenislead.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-reply me-1"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
