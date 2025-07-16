@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F; border-color: #1DCD9F;">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i>Tambah Kode Kampanye</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('kampanye.store') }}" method="POST">
                @csrf

                <!-- Pilih User -->
                <div class="form-group">
                    <label for="user_id"><i class="fas fa-user mr-1" style="color: #1DCD9F;"></i>User</label>
                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="" disabled selected>Pilih user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->username }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Kode Kampanye -->
                <div class="form-group">
                    <label for="kode_kampanye"><i class="fas fa-bullhorn mr-1" style="color: #1DCD9F;"></i>Kode Kampanye</label>
                    <input type="text" class="form-control @error('kode_kampanye') is-invalid @enderror"
                        id="kode_kampanye" name="kode_kampanye"
                        placeholder="Masukkan kode kampanye" required
                        value="{{ old('kode_kampanye') }}">
                    @error('kode_kampanye')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">Simpan</button>
                <a href="{{ route('kampanye.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
