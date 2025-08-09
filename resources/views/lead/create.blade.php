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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        {{ session('error') }}
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


<div class="card card-primary card-outline  " style="border-color: #00518d;">
            <div class="card-header ">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1" style="color: #00518d;"></i>Tambah Lead Anda</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('lead.store') }}" method="POST">
                @csrf

                <!-- Jumlah Lead -->
                <div class="form-group">
                    <label for="jumlah_lead"><i class="fas fa-users mr-1" style="color: #00518d;"></i>Jumlah Lead</label>
                    <input type="number" class="form-control @error('jumlah_lead') is-invalid @enderror"
                           id="jumlah_lead" name="jumlah_lead"
                           placeholder="Masukkan jumlah lead" required
                           value="{{ old('jumlah_lead') }}">
                    @error('jumlah_lead')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div class="form-group">
                    <label for="tanggal"><i class="fas fa-calendar-alt mr-1" style="color: #00518d;"></i>Tanggal</label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                           id="tanggal" name="tanggal"
                           required value="{{ old('tanggal') }}">
                    @error('tanggal')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

<!-- Jenis Lead -->
<div class="form-group">
    <label for="jenis_lead_id"><i class="fas fa-list-alt mr-1" style="color: #00518d;"></i>Jenis Lead</label>
    <select class="form-control @error('jenis_lead_id') is-invalid @enderror" id="jenis_lead_id" name="jenis_lead_id" required>
        <option value="" disabled selected>Pilih jenis lead</option>
        @foreach($jenis_lead as $jl)
            <option value="{{ $jl->id }}" {{ old('jenis_lead_id') == $jl->id ? 'selected' : '' }}>{{ $jl->jenis }}</option>
        @endforeach
    </select>
    @error('jenis_lead_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>


                <button type="submit" class="btn btn-success mt-3">Simpan</button>
                
            </form>
        </div>
    </div>
</div>
@endsection
