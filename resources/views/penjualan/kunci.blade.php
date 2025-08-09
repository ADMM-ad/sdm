@extends('masterlayout')

@section('content')
<div class="container mt-3">
  <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-lock mr-1" style="color: #00518d;"></i>Setting HPP</h3>
        </div>
        <div class="card-body">
    <form action="{{ route('penjualan.update_kunci', $penjualan->id) }}?{{ http_build_query(request()->query()) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="kunci_hpp">Tanpa HPP ?</label>
            <select name="kunci_hpp" class="form-control">
                <option value="">Silahkan Memilih Ya dan Tidak</option>
                <option value="ya" {{ $penjualan->kunci_hpp == 'ya' ? 'selected' : '' }}>Ya</option>
                <option value="tidak" {{ $penjualan->kunci_hpp == 'tidak' ? 'selected' : '' }}>Tidak</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-2"><i class="fas fa-save me-1"></i> Simpan</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary mt-2"> <i class="fas fa-reply me-1"></i>Kembali</a>
    </form>
</div>
</div>
</div>
@endsection
