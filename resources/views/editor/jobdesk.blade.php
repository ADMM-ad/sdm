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

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


<form method="GET">
    <div class="row">
        <div class="col-md-6 mb-2">
            <input type="text" id="daterange" name="daterange" class="form-control"
                   placeholder="Pilih rentang tanggal"
                   value="{{ request('daterange') }}">
        </div>
        <div class="col-md-6 mb-2">
            <select name="platform" id="platform" class="form-control">
                <option value="">Semua Platform</option>
                <option value="nonshopee" {{ request('platform') == 'nonshopee' ? 'selected' : '' }}>Non Shopee</option>
                <option value="shopee" {{ request('platform') == 'shopee' ? 'selected' : '' }}>Shopee</option>
            </select>
        </div>
        <div class="col-md-6 mb-2">
    <select name="produk" id="produk" class="form-control">
        <option value="">Semua Produk</option>
        @foreach($produkList as $produk)
            <option value="{{ $produk->id }}" {{ request('produk') == $produk->id ? 'selected' : '' }}>
                {{ $produk->nama_produk }}
            </option>
        @endforeach
    </select>
</div>

        <div class="col-md-6 mb-2">
          
    <div class="d-flex flex-wrap justify-content-between" style="gap: 0.5rem;"">
                
                    <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                
             
                    <a href="{{ route('editor.jobdesk') }}" class="btn btn-secondary flex-fill">Reset</a>
             
                    <button type="button" class="btn btn-success flex-fill" data-toggle="modal" data-target="#bulkAssignModal">
                        Pilih Editor
                    </button>
                
            </div>
        </div>
    </div>
</form>

    @if($penjualan->isEmpty())
        <div class="alert alert-info">
            Tidak ada jobdesk baru.
        </div>
    @else
<!-- Tabel Produk -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1" style="color: #00518d;"></i>Pilihan Jobdesk</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>No</th>
                    <th>Tanggal Pesanan</th>
                    <th>Jam Pesanan</th>
                    <th>Metode Pengiriman</th>
                    <th>Nama CS</th>
                    <th>Nama Pembeli</th>
                    <th>No Resi</th>
                    <th>Status Pesanan</th>
                    <th>Detail</th>
                    <th>Produk</th>
                    <th>Variasi</th>
                    <th>Jumlah</th>
                    <th>Nama Editor</th>
                    <th>Waktu Diambil</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($penjualan as $p)
                    @foreach($p->detailPenjualan as $index => $dp)
                        <tr>
                            @if($index === 0)
                            <td rowspan="{{ $p->detailPenjualan->count() }}">
    <input type="checkbox" class="export-checkbox" value="{{ $p->id }}">
</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ ($p->tanggal) }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->metode_pengiriman }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi ?? '-' }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pesanan ?? '-' }}</td>

                                <td rowspan="{{ $p->detailPenjualan->count() }}" style="white-space: pre-wrap; word-wrap: break-word; max-width: 250px;">{{ $p->detail }}</td>
                            @endif
                            <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $dp->nama_variasi ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                            @if($index === 0)
                            <td rowspan="{{ $p->detailPenjualan->count() }}">
    <form method="POST" action="{{ route('editor.jobdesk.updateEditor') }}">
        @csrf
        <input type="hidden" name="penjualan_id" value="{{ $p->id }}">
        <select name="user_id" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">Pilih Editor</option>
            @foreach($editors as $editor)
                <option value="{{ $editor->id }}"
                    {{ optional($p->jobdeskEditor)->user_id == $editor->id ? 'selected' : '' }}>
                    {{ $editor->name }}
                </option>
            @endforeach
        </select>
    </form>
</td>

                            <td rowspan="{{ $p->detailPenjualan->count() }}">
    {{ optional($p->jobdeskEditor)->created_at ? \Carbon\Carbon::parse($p->jobdeskEditor->created_at)->format('d M Y H:i') : '-' }}
</td>

                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    <form method="POST" action="{{ route('editor.jobdesk.ambil') }}">
                                        @csrf
                                        <input type="hidden" name="penjualan_id" value="{{ $p->id }}">
                                        <button type="button" class="btn btn-success btn-sm btn-confirm" 
    data-id="{{ $p->id }}"
    data-toggle="modal"
    data-target="#confirmModal">
    Ambil
</button>

                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
        </div>
            </div>
                </div>
                
               
    @endif
</div>



<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="confirmForm" method="POST" action="{{ route('editor.jobdesk.ambil') }}">
        @csrf
        <input type="hidden" name="penjualan_id" id="modal_penjualan_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Ambil Jobdesk</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin mengambil jobdesk ini?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
            <button type="submit" class="btn btn-success">Ya, Ambil</button>
          </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Bulk Assign Editor -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" role="dialog" aria-labelledby="bulkAssignModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="bulkEditForm" method="POST" action="{{ route('editor.jobdesk.bulkUpdateEditor') }}">
        @csrf
        <input type="hidden" name="selected_ids" id="selected_ids">
        <input type="hidden" name="user_id" id="modal_editor_id">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="bulkAssignModalLabel">Assign Editor</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="editor_select">Pilih Editor</label>
              <select class="form-control" id="editor_select">
                @foreach($editors as $editor)
                    <option value="{{ $editor->id }}">{{ $editor->name }}</option>
                @endforeach
              </select>
            </div>
            <div id="modalError" class="text-danger"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-success" id="submitBulkAssign">Simpan</button>
          </div>
        </div>
    </form>
  </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn-confirm');
        const modalInput = document.getElementById('modal_penjualan_id');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                modalInput.value = id;
            });
        });
    });

      // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.export-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Submit form dari modal
    document.getElementById('submitBulkAssign').addEventListener('click', function() {
        const selected = [...document.querySelectorAll('.export-checkbox:checked')].map(cb => cb.value);
        const editorId = document.getElementById('editor_select').value;

        if (selected.length === 0) {
            document.getElementById('modalError').textContent = "Pilih minimal satu data penjualan.";
            return;
        }

        document.getElementById('modalError').textContent = "";

        // Set data ke hidden input
        document.getElementById('selected_ids').value = selected.join(',');
        document.getElementById('modal_editor_id').value = editorId;

        // Submit
        document.getElementById('bulkEditForm').submit();
    });
    
</script>

@endsection
