@extends('masterlayout')

@section('content')

<div class="container mt-3">
    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F">
            <h3 class="card-title">
                <i class="fas fa-user mr-2"></i>Edit Profil Anda
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('profil.update') }}" method="POST">
                @csrf

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-user me-1"></i> Nama
                    </label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user-tag me-1"></i> Username
                    </label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                    @error('username')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1"></i> Password (Kosongkan jika tidak ingin ganti)
                    </label>
                    <input type="password" name="password" class="form-control">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock me-1"></i> Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                {{-- Tombol --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('profil.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
