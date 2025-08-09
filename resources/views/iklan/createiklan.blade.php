@extends('masterlayout')

@section('content')
<div class="container mt-3">
 <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle mr-1" style="color:#00518d;"></i>Tambah Iklan </h3>
        </div>
        <div class="card-body">
    <form action="{{ route('iklan.store') }}" method="POST">
        @csrf
       <div class="form-group">
                    <label for="awal_pelaporan"><i class="fas fa-calendar-alt mr-1" style="color:#00518d;"></i>Tanggal Awal Pelaporan</label>
                    <input type="date" name="awal_pelaporan" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="kode_kampanye_id"> <i class="fas fa-bullhorn mr-1" style="color:#00518d;"></i>Kode Kampanye</label>
                    <select name="kode_kampanye_id" class="form-control" required>
                        <option value="">Silahkan Memilih Kode Iklan</option>
                        @foreach($kampanyes as $kampanye)
                            <option value="{{ $kampanye->id }}">{{ $kampanye->kode_kampanye }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="jenis_lead_id"><i class="fas fa-random mr-1" style="color:#00518d;"></i>Jenis Lead</label>
                    <select name="jenis_lead_id" class="form-control">
                        <option value="">Silahkan Memilih Jenis Lead Iklan</option>
                        @foreach($jenisLeads as $jl)
                            <option value="{{ $jl->id }}">{{ $jl->jenis }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="hasil"> <i class="fas fa-chart-line mr-1" style="color:#00518d;"></i>Hasil</label>
                    <input type="number" name="hasil" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="jumlah_dibelanjakan"><i class="fas fa-money-bill-wave mr-1" style="color:#00518d;"></i>Jumlah Dibelanjakan</label>
                    <input type="number" name="jumlah_dibelanjakan" class="form-control" required>
                </div>

        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Simpan</button>
        <a href="{{ route('iklan.import') }}" class="btn btn-secondary"><i class="fas fa-reply me-1"></i> kembali</a>
    </form>
</div>
</div>
</div>
@endsection
