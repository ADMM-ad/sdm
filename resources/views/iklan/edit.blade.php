@extends('masterlayout')

@section('content')
<div class="container mt-3">
  <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1" style="color: #00518d;"></i>Edit Kode Kampanye</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('kampanye.update', $kampanye->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Pilih User -->
                <div class="form-group">
                    <label for="user_id"><i class="fas fa-user mr-1" style="color: #00518d;"></i>User</label>
                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="" disabled>Silahkan Memilih CS</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $kampanye->user_id == $user->id ? 'selected' : '' }}>
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
                    <label for="kode_kampanye"><i class="fas fa-bullhorn mr-1" style="color: #00518d;"></i>Kode Kampanye</label>
                    <input type="text" class="form-control @error('kode_kampanye') is-invalid @enderror"
                        id="kode_kampanye" name="kode_kampanye"
                        value="{{ $kampanye->kode_kampanye }}"
                        required>
                    @error('kode_kampanye')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
<!-- Jenis Lead -->
<div class="form-group">
    <label for="jenis_lead_id"><i class="fas fa-filter mr-1" style="color: #00518d;"></i>Jenis Lead</label>
    <select class="form-control @error('jenis_lead_id') is-invalid @enderror" id="jenis_lead_id" name="jenis_lead_id">
        <option value="">Pilih jenis lead (Opsional)</option>
        @foreach($jenisLeads as $jenis)
            <option value="{{ $jenis->id }}" {{ $kampanye->jenis_lead_id == $jenis->id ? 'selected' : '' }}>
                {{ $jenis->jenis }}
            </option>
        @endforeach
    </select>
    @error('jenis_lead_id')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>

                <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('kampanye.index') }}" class="btn btn-secondary mt-3"><i class="fas fa-reply me-1"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
