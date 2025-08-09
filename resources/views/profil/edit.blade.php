@extends('masterlayout')

@section('content')

<div class="container mt-3">
  <div class="card card-primary card-outline  " style="border-color: #00518d;">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-id-card" style="color: #00518d"></i>
                Edit Profil Anda
            </h4>
        </div>
        
        <div class="card-body">
            <form action="{{ route('profil.update') }}" method="POST">
                @csrf

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="fas fa-user me-1" style="color: #00518d"></i> Nama
                    </label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Username --}}
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user-tag me-1" style="color: #00518d"></i> Username
                    </label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                    @error('username')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1" style="color: #00518d"></i> Password (Kosongkan jika tidak ingin ganti)
                    </label>
                    <div class="input-group">
        <input type="password" name="password" id="password" class="form-control">
        <span class="input-group-text" onclick="togglePassword('password', this)" style="cursor: pointer;">
            <i class="fas fa-eye"></i>
        </span>
    </div>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-lock me-1" style="color: #00518d"></i> Konfirmasi Password Baru
                    </label>
                    <div class="input-group">
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        <span class="input-group-text" onclick="togglePassword('password_confirmation', this)" style="cursor: pointer;">
            <i class="fas fa-eye"></i>
        </span>
    </div>
                </div>

                {{-- Tombol --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan 
                    </button>
                    <a href="{{ route('profil.index') }}" class="btn btn-secondary">
                        <i class="fas fa-reply me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId, el) {
        const input = document.getElementById(fieldId);
        const icon = el.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>


@endsection
