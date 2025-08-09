@extends('masterlayout')

@section('content')
<div class="container mt-3">
   <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1" style="color: #00518d;"></i>Edit Voucher Shopee</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('user.updateVoucher', $user->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="kode_voucher" class="form-label">
                        <i class="fas fa-ticket-alt me-1" style="color: #00518d;"></i> Kode Voucher
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
                        <i class="fas fa-reply me-1"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
