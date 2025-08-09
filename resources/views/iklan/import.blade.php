@extends('masterlayout')

@section('content')
<div class="container mt-3">
 @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif


  <div class="card card-primary card-outline " style="border-color: #00518d;">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
            <!-- Judul di kiri -->
            <h3 class="card-title mb-0">
                <i class="fas fa-bullhorn mr-1" style="color: #00518d;"></i>Import Data Iklan
            </h3>
            <!-- Tombol di kanan -->
            <div class="d-flex justify-content-end">
                <a href="{{ route('iklan.downloadTemplate') }}" class="btn btn-info btn-sm mr-1">
                    <i class="fas fa-download"></i>
                </a>
                <a href="{{ route('iklan.create') }}" class="btn btn-sm"  style="background-color: #00518d;">
                    <i class="fas fa-plus-circle"  style="color: #ffffff;"></i>
                </a>
            </div>
        
        </div>
        </div>
        <div class="card-body">
           

            <form action="{{ route('iklan.import') }}" method="POST" enctype="multipart/form-data" >
                @csrf
                <div class="form-group">
                    <h3 class="card-title mb-2" for="file">Upload File Excel .xlsx</h3>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm"> <i class="fas fa-upload"></i></button>
               
            </form>

            
 </div>
  </div>     

            
<div class="row">
  
    <div class="col-12">
        <div class="card">
<div class="card-header">
    <div class="row align-items-center w-100">
        
        <!-- Judul di kiri -->
        <div class="col-md-4 col-12 mb-2 mb-md-0">
            <h3 class="card-title">
                <i class="fas fa-bullhorn mr-2" style="color: #00518d;"></i>Laporan Iklan
            </h3>
        </div>

        <!-- Form filter di kanan -->
        <div class="col-md-8 col-12">
            <form method="GET" action="{{ route('iklan.import') }}" class="row g-2 justify-content-md-end">
                
                <!-- Input tanggal -->
                <div class="col-12 col-md-auto">
                    <input type="text" name="daterange" id="daterange" 
                           class="form-control"
                           placeholder="Pilih rentang tanggal"
                           value="{{ request('daterange') }}">
                </div>

                <!-- Tombol Filter -->
                <div class="col-4 col-md-auto mt-2 mt-2 mt-md-0">
                    <button type="submit" class="btn btn-primary w-100" style="background-color: #00518d; border-color: #00518d;">Filter</button>
                </div>

                <!-- Tombol Reset -->
                <div class="col-4 col-md-auto mt-2 mt-md-0">
                    <a href="{{ route('iklan.import') }}" class="btn btn-secondary w-100">Reset</a>
                </div>

                <!-- Tombol Jenis Lead -->
                <div class="col-4 col-md-auto mt-2 mt-md-0">
                    <a href="#" class="btn btn-warning w-100" 
                       data-toggle="modal" data-target="#editJenisLeadModal">
                        Jenis 
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Bulk Edit (hidden) -->
    <form id="bulkEditForm" method="POST" action="{{ route('iklan.bulkUpdateJenisLead') }}">
        @csrf
        <input type="hidden" name="selected_ids" id="selected_ids">
        <input type="hidden" name="jenis_lead_id" id="modal_jenis_lead_id">
    </form>
</div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>No</th>
                            <th>Awal Pelaporan</th>
                            <th>Nama CS</th>
                            <th>Nama Kampanye</th>
                            <th>Hasil</th>
                            <th>Jumlah yang Dibelanjakan</th>
                            <th>Jenis Lead</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($iklans as $index => $iklan)
                            <tr>
                                <td><input type="checkbox" class="export-checkbox" value="{{ $iklan->id }}"></td>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($iklan->awal_pelaporan)->format('d-m-Y') }}</td>
                                <td>{{ $iklan->kampanye->user->name ?? '-' }}</td>
                                <td>{{ $iklan->kampanye->kode_kampanye ?? '-' }}</td>
                                <td>{{ $iklan->hasil }}</td>
                                <td>Rp {{ number_format($iklan->jumlah_dibelanjakan, 0, ',', '.') }}</td>
                                <td>{{ $iklan->jenisLead->jenis ?? '-' }}</td> <!-- Tambahan -->
                                <td>
    <a href="{{ route('iklan.edit', $iklan->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
    <button onclick="confirmDelete('{{ route('iklan.destroy', $iklan->id) }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></button>
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
              </div>
                </div>
                  </div>
          

       
   

</div>



<!-- Modal Edit Jenis Lead -->
<div class="modal fade" id="editJenisLeadModal" tabindex="-1" role="dialog" aria-labelledby="editJenisLeadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editJenisLeadModalLabel">Edit Jenis Lead</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <label for="jenis_lead_select">Pilih Jenis Lead</label>
            <select class="form-control" id="jenis_lead_select">
                @foreach($jenisLeads as $jl)
                    <option value="{{ $jl->id }}">{{ $jl->jenis }}</option>
                @endforeach
            </select>
        </div>
        <div id="modalError" class="text-danger"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-success" id="submitEditJenisLead">Simpan</button>
      </div>
    </div>
  </div>
</div>


<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.export-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Submit form dari modal
    document.getElementById('submitEditJenisLead').addEventListener('click', function() {
        const selected = [...document.querySelectorAll('.export-checkbox:checked')].map(cb => cb.value);
        const jenisLeadId = document.getElementById('jenis_lead_select').value;

        if (selected.length === 0) {
            document.getElementById('modalError').textContent = "Pilih minimal satu data iklan.";
            return;
        }

        // Set data ke form tersembunyi
        document.getElementById('selected_ids').value = selected.join(',');
        document.getElementById('modal_jenis_lead_id').value = jenisLeadId;

        // Submit form
        document.getElementById('bulkEditForm').submit();
    });
</script>


<!-- Sudah benar, tidak perlu diubah -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(action) {
        document.getElementById('deleteForm').action = action;
        $('#confirmDeleteModal').modal('show');
    }
</script>

@endsection
