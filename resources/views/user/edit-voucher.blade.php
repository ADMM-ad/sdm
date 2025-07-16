@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F">
            <h3 class="card-title">
                <i class="fas fa-ticket-alt mr-2"></i>Edit Kode Voucher
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ route('user.updateVoucher', $user->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="kode_voucher" class="form-label">
                        <i class="fas fa-ticket-alt me-1"></i> Kode Voucher
                    </label>
                    <input type="number" name="kode_voucher" class="form-control" value="{{ old('kode_voucher', $user->kode_voucher) }}">
                    @error('kode_voucher')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                    <a href="{{ route('user.cs') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
