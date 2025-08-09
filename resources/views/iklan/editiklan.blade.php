@extends('masterlayout')

@section('content')
<div class="container mt-3">
 <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1" style="color: #00518d;"></i>Edit Iklan</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('iklan.update', $iklan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="awal_pelaporan"><i class="fas fa-calendar-alt mr-1" style="color: #00518d;"></i>Tanggal Awal Pelaporan</label>
                    <input type="date" name="awal_pelaporan" class="form-control"
    value="{{ \Carbon\Carbon::parse($iklan->awal_pelaporan)->format('Y-m-d') }}" required>

                </div>

                <div class="form-group">
                    <label for="kode_kampanye_id"><i class="fas fa-bullhorn mr-1" style="color:#00518d;"></i>Kode Kampanye</label>
                    <select name="kode_kampanye_id" class="form-control" required>
                        @foreach($kampanyes as $kampanye)
                            <option value="{{ $kampanye->id }}" {{ $iklan->kode_kampanye_id == $kampanye->id ? 'selected' : '' }}>
                                {{ $kampanye->kode_kampanye }} - {{ $kampanye->user->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="hasil"><i class="fas fa-chart-line mr-1" style="color:#00518d;"></i>Hasil</label>
                    <input type="number" name="hasil" class="form-control" value="{{ $iklan->hasil }}" required>
                </div>

                <div class="form-group">
                    <label for="jumlah_dibelanjakan"><i class="fas fa-money-bill-wave mr-1" style="color:#00518d;"></i>Jumlah Dibelanjakan (Rp)</label>
                    <input type="number" name="jumlah_dibelanjakan" class="form-control" value="{{ $iklan->jumlah_dibelanjakan }}" required>
                </div>

                <div class="form-group">
                    <label for="jenis_lead_id"><i class="fas fa-random mr-1" style="color:#00518d;"></i>Jenis Lead</label>
                    <select name="jenis_lead_id" class="form-control">
                        <option value="">-- Pilih --</option>
                        @foreach($jenisLeads as $jl)
                            <option value="{{ $jl->id }}" {{ $iklan->jenis_lead_id == $jl->id ? 'selected' : '' }}>
                                {{ $jl->jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>
  <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan </button>
                <a href="{{ route('iklan.import') }}" class="btn btn-secondary"><i class="fas fa-reply me-1"></i> Kembali</a>
              
            </form>
        </div>
    </div>
</div>
@endsection
