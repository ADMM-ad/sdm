@extends('masterlayout')

@section('content')
<div class="container mt-3">
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F;">
            <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i> Tambah Akun Baru</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('user.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name"><i class="fas fa-user mr-1" style="color: #1DCD9F;"></i>Nama Lengkap</label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username"><i class="fas fa-user-tag mr-1" style="color: #1DCD9F;"></i>Username</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username') }}" placeholder="Masukkan username" required>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock mr-1" style="color: #1DCD9F;"></i>Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" placeholder="Masukkan password minimal 6 karakter" required>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm"><i class="bi bi-shield-lock-fill mr-1" style="color: #1DCD9F;"></i>Konfirmasi Password</label>
                    <input id="password-confirm" type="password" class="form-control"
                           name="password_confirmation" placeholder="Ulangi password" required>
                </div>

<div class="form-group">
    <label for="kode_voucher"><i class="fas fa-ticket-alt mr-1" style="color: #1DCD9F;"></i>Kode Voucher Shopee</label>
    <input id="kode_voucher" type="number" class="form-control @error('kode_voucher') is-invalid @enderror"
           name="kode_voucher" value="{{ old('kode_voucher') }}" placeholder="Masukkan kode voucher (opsional)">
    @error('kode_voucher')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>


                <div class="form-group">
                    <label for="role"><i class="fas fa-briefcase mr-1" style="color: #1DCD9F;"></i>Role / Jabatan</label>
                    <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customerservice" {{ old('role') == 'customerservice' ? 'selected' : '' }}>Customer Service</option>
                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                    </select>
                    @error('role')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-block text-white" style="background-color: #1DCD9F;">
                    Tambahkan Pengguna
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
