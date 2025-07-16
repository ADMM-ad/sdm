@extends('masterlayout')

@section('content')
<div class="container mt-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-primary mt-2">
        <div class="card-header" style="background-color: #1DCD9F; border-color: #1DCD9F;">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i>Edit Lead</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('lead.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Jumlah Lead -->
                <div class="form-group">
                    <label for="jumlah_lead"><i class="fas fa-users mr-1" style="color: #1DCD9F;"></i>Jumlah Lead</label>
                    <input type="number"
                           class="form-control @error('jumlah_lead') is-invalid @enderror"
                           id="jumlah_lead"
                           name="jumlah_lead"
                           value="{{ old('jumlah_lead', $lead->jumlah_lead) }}"
                           required>
                    @error('jumlah_lead')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div class="form-group">
                    <label for="tanggal"><i class="fas fa-calendar-alt mr-1" style="color: #1DCD9F;"></i>Tanggal</label>
                    <input type="date"
                           class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal"
                           name="tanggal"
                           value="{{ old('tanggal', $lead->tanggal) }}"
                           required>
                    @error('tanggal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">Perbarui</button>
                <a href="{{ route('lead.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
