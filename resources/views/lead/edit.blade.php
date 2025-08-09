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

    <div class="card card-primary card-outline " style="border-color: #00518d;">
            <div class="card-header ">
            <h3 class="card-title"><i class="fas fa-edit mr-1" style="color: #00518d;"></i>Edit Lead</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('lead.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Jumlah Lead -->
                <div class="form-group">
                    <label for="jumlah_lead"><i class="fas fa-users mr-1" style="color: #00518d;"></i>Jumlah Lead</label>
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
                    <label for="tanggal"><i class="fas fa-calendar-alt mr-1" style="color: #00518d;"></i>Tanggal</label>
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

<!-- Jenis Lead -->
<div class="form-group">
    <label for="jenis_lead_id">
        <i class="fas fa-tags mr-1" style="color: #00518d;"></i>Jenis Lead
    </label>
    <select name="jenis_lead_id" id="jenis_lead_id"
            class="form-control @error('jenis_lead_id') is-invalid @enderror" required>
        <option value="">-- Pilih Jenis Lead --</option>
        @foreach($jenisLeadList as $jenis)
            <option value="{{ $jenis->id }}"
                {{ old('jenis_lead_id', $lead->jenis_lead_id) == $jenis->id ? 'selected' : '' }}>
                {{ $jenis->jenis }}
            </option>
        @endforeach
    </select>
    @error('jenis_lead_id')
        <span class="invalid-feedback">{{ $message }}</span>
    @enderror
</div>



                <button type="submit" class="btn btn-warning mt-3"> <i class="fas fa-edit mr-1"></i>Update</button>
                <a href="{{ route('lead.index') }}" class="btn btn-secondary mt-3">  <i class="fas fa-reply mr-1"></i>Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
