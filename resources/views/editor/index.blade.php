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

@if($jobdesks->isEmpty())
    <div class="alert alert-info">
        Tidak ada jobdesk yang sedang diproses.
    </div>
@else
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1" style="color: #31beb4;"></i> Jobdesk Saya</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-bordered text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pesanan</th>
                            <th>Tanggal Mengerjakan</th>
                            <th>Metode Pengiriman</th>
                            <th>Nama CS</th>
                            <th>Nama Pembeli</th>
                            <th>No Resi</th>
                            <th>Status Pesanan</th>
                            <th>Detail</th>
                            <th>Produk</th>
                            <th>Variasi</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @foreach($jobdesks as $jd)
                            @php $p = $jd->penjualan; @endphp
                            @foreach($p->detailPenjualan as $index => $dp)
                                <tr>
                                    @if($index === 0)
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ ($p->tanggal) }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">
        {{ optional($p->jobdeskEditor)->created_at ? \Carbon\Carbon::parse($p->jobdeskEditor->created_at)->format('d M Y H:i') : '-' }}
    </td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->metode_pengiriman }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi ?? '-' }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pesanan ?? '-' }}</td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->detail }}</td>
                                    @endif
                                    <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $dp->nama_variasi ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                                    @if($index === 0)
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">
                                            <span class="badge badge-{{ $jd->status === 'selesai' ? 'success' : 'warning' }}">
                                                {{ ucfirst($jd->status) }}
                                            </span>
                                        </td>
                                        <td rowspan="{{ $p->detailPenjualan->count() }}">
    <!-- Tombol trigger modal -->
    <button type="button" class="btn btn-sm btn-primary btn-selesai"
        data-id="{{ $jd->id }}"
        data-toggle="modal"
        data-target="#selesaiModal">
        Selesai
    </button>
    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ route('editor.jobdesk.hapus', ['id' => $jd->id]) }}')">
    Cancel
</button>
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

<!-- Modal Konfirmasi Selesai -->
<div class="modal fade" id="selesaiModal" tabindex="-1" role="dialog" aria-labelledby="selesaiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('editor.jobdesk.selesai') }}">
        @csrf
        <input type="hidden" name="jobdesk_id" id="modal_jobdesk_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="selesaiModalLabel">Konfirmasi Selesai</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Apakah Anda yakin ingin menandai jobdesk ini sebagai <strong>selesai</strong>?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
            <button type="submit" class="btn btn-primary">Ya, Tandai Selesai</button>
          </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Batal</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">Apakah Anda yakin tidak jadi mengerjakan ini?</div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn-selesai');
        const modalInput = document.getElementById('modal_jobdesk_id');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const jobdeskId = this.getAttribute('data-id');
                modalInput.value = jobdeskId;
            });
        });
    });

      function confirmDelete(action) {
        var form = document.getElementById('deleteForm');
        form.action = action;
        $('#confirmDeleteModal').modal('show');
    }s
</script>

@endsection
