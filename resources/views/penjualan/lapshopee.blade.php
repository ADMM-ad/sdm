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

    <form method="GET" action="{{ route('penjualan.lapshopee') }}" class="mb-3">
        <div class="row">
            {{-- Baris Pertama: Dropdown CS dan Tanggal --}}
            <div class="col-md-6 mb-2">
                <select name="cs" id="cs" class="form-control">
                    <option value="">Semua Customer Service</option>
                    @foreach($semuaUser as $user)
                    <option value="{{ $user->id }}" {{ request('cs') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <input type="text" id="daterange" name="daterange" class="form-control"
                    placeholder="Pilih rentang tanggal" value="{{ request('daterange') }}">
            </div>
          

            

            <div class="col-md-6 mb-2">
                <input type="text" name="nama_pembeli" class="form-control" placeholder="Cari Nama Pembeli"
                    value="{{ request('nama_pembeli') }}">
            </div>

            <div class="col-md-6 mb-2">
    <select name="status_filter" id="status_filter" class="form-control">
        <option value="">Semua Status Pesanan</option>
        <option value="batal" {{ request('status_filter') === 'batal' ? 'selected' : '' }}>Batal</option>
    </select>
</div>

           

            {{-- Baris Kedua: Semua Button tampil dalam 1 baris penuh (5 kolom rata) --}}
         <div class="col-12">
                <div class="row">
                    <div class="col-md-2 col-6 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
                        <a href="{{ route('penjualan.lapshopee') }}" class="btn btn-secondary w-100">Reset</a>
                    </div>
               
                    <div class="col-md-2 col-6 mb-2">
                        <button type="button" class="btn btn-info w-100" id="setWithoutHppButton">Tanpa HPP Ongkir</button>
                    </div>
                    <div class="col-md-2 col-6 mb-2">
    <button type="button" class="btn btn-warning w-100" id="beriHppButton">
        Beri HPP 
    </button>
</div>


                    <div class="col-md-2 col-6 mb-2">
    <button type="button" class="btn btn-dark w-100" data-toggle="modal" data-target="#uploadModalShopee">
        Biaya Shopee
    </button>
</div>
<div class="col-md-2 col-6 mb-2">
    <a href="{{ route('download.template.shopee') }}" class="btn btn-success w-100">
       Template Biaya
    </a>
</div>

                     </div>
                 
                 </div>
        </div>
    </form>


    @if($penjualan->isEmpty())
    <div class="alert alert-info">Belum ada data penjualan Shopee.</div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice mr-1" style="color: #00518d;"></i>Laporan Closing Penjualan Shopee
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-bordered text-nowrap">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jam Input</th>
                                <th>Status Pesanan</th>
                                <th>Alasan Batal</th>
                                <th>Status Pembatalan</th>
                                <th>Nama CS</th>
                                <th>No Pesanan</th>
                                <th>Nama Pembeli</th>
                                <th>Alamat</th>
                                <th>No HP</th>
                                <th>Kode Pos</th>
                                <th>Metode Pengiriman</th>
                                <th>Metode Pembayaran</th>
                                <th>Ekspedisi</th>
                                <th>No Resi</th>
                                

                                <th>Produk</th>
                                <th>Detail</th>
                                <th>Variasi</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Total HPP</th>
                                <th>Ongkir</th>
                                <th>Cashback</th>
                                <th>Biaya COD</th>
                             
                              
                                <th>Total Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $penjualan->firstItem(); @endphp
                            @foreach($penjualan as $p)
                            @if($p->detailPenjualan->isEmpty())
                            <tr>
                                <td><input type="checkbox" class="export-checkbox" value="{{ $p->id }}"></td>
                                <td>{{ $no++ }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d-H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}</td>
                                <td>{{$p->status_pesanan}}</td>
                                <td>{{$p->alasan_batal}}</td>
                                <td>{{$p->status_pembatalan}}</td>
                                <td>{{ $p->user->name ?? '-' }}</td>
                                <td>{{ $p->order_id }}</td>
                                <td>{{ $p->nama_pembeli }} </td>
                                <td>{{ $p->alamat }} ({{ $p->kodepos }})</td>
                                <td>{{ $p->no_hp }}</td>
                                <td>{{ $p->kodepos }}</td>
                                <td>{{ strtoupper($p->metode_pengiriman) }}</td>
                                <td>{{ $p->metode_pembayaran }}</td>
                                <td>{{ $p->kurir }}</td>
                                <td>{{$p->no_resi}}</td>
                                

                                <td colspan="4" class="text-center text-muted">Belum ada produk</td>
                                <td>{{ 'Rp ' . number_format($p->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->total_hpp ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->ongkir ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->cashback ?? 0, 0, ',', '.') }}</td>
                                <td>{{ 'Rp ' . number_format($p->biaya_cod ?? 0, 0, ',', '.') }}</td>
                              
                                <td><strong>Rp{{ number_format($p->total_bayar ?? 0, 0, ',', '.') }}</strong></td>
                                <td>
    <a href="{{ route('penjualan.edit_kunci', $p->id) }}"
   class="btn {{ $p->kunci_hpp === 'ya' ? 'btn-danger' : 'btn-success' }} btn-sm">
   <i class="fas fa-lock"></i>
</a>

                                 

                                    @php
                                    $queryString = http_build_query(request()->only('cs', 'daterange',
                                    'jenis_produk', 'nama_pembeli', 'waktu_input'));
                                    @endphp

                                </td>
                            </tr>
                            @else
                            @foreach($p->detailPenjualan as $index => $dp)
                            <tr>
                                @if($index === 0)
                                <td rowspan="{{ $p->detailPenjualan->count() }}"><input type="checkbox"
                                        class="export-checkbox" value="{{ $p->id }}"></td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $no++ }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d-H:i') }}
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }}
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pesanan}}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->alasan_batal}}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->status_pembatalan}}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->user->name ?? '-' }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->order_id }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->nama_pembeli }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->alamat }} ({{ $p->kodepos }})
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_hp }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->kodepos }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ strtoupper($p->metode_pengiriman)
                                    }}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->metode_pembayaran}}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->kurir}}</td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->no_resi}}</td>
                                

                                @endif
                                <td class="px-3 py-2">{{ $dp->produk->nama_produk ?? '-' }}</td>
                                @if($index === 0)
                                <td rowspan="{{ $p->detailPenjualan->count() }}">{{ $p->detail}}</td>
                                @endif
                                <td class="px-3 py-2">{{ $dp->nama_variasi ?? '-' }}</td>
                                <td class="px-3 py-2">{{ $dp->jumlah }}</td>
                                <td class="px-3 py-2">{{'Rp ' . number_format($dp->total_harga, 0, ',', '.') }}</td>

                                @if($index === 0)
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->total_hpp ?? 0, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->ongkir ?? 0, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->cashback ?? 0, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    {{ 'Rp ' . number_format($p->biaya_cod ?? 0, 0, ',', '.') }}
                                </td>
                            

                                <td rowspan="{{ $p->detailPenjualan->count() }}">
                                    <strong>Rp{{ number_format($p->total_bayar ?? 0, 0, ',', '.') }}</strong>
                                </td>

                                <td rowspan="{{ $p->detailPenjualan->count() }}">
   <a href="{{ route('penjualan.edit_kunci', $p->id) }}"
   class="btn {{ $p->kunci_hpp === 'ya' ? 'btn-danger' : 'btn-success' }} btn-sm">
   <i class="fas fa-lock"></i>
</a>

                                    

                                    @php
                                    $queryString = http_build_query(request()->only('cs', 'daterange',
                                    'jenis_produk', 'nama_pembeli', 'waktu_input'));
                                    @endphp

                                  

                                </td>
                                @endif
                            </tr>
                            @endforeach
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="d-flex justify-content-end mt-2">
        {{ $penjualan->links('pagination::bootstrap-4') }}
    </div>

</div>


<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
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


<div class="modal fade" id="confirmWithoutHppModal" tabindex="-1" aria-labelledby="confirmWithoutHppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmWithoutHppModalLabel">Konfirmasi Tanpa HPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Anda yakin ingin mengatur HPP menjadi 0 dan mengunci HPP untuk transaksi yang dipilih?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="withoutHppForm" method="POST" action="{{ route('penjualan.setWithoutHpp') }}">
                    @csrf
                    <input type="hidden" name="selected_ids" id="withoutHppSelectedIds">
                    <button type="submit" class="btn btn-info">Ya, Atur HPP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmBeriHppModal" tabindex="-1" aria-labelledby="confirmBeriHppModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Beri HPP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghitung ulang HPP dan membuka kunci untuk transaksi yang dipilih?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="beriHppForm" method="POST" action="{{ route('penjualan.beriHpp') }}">
                    @csrf
                    <input type="hidden" name="selected_ids" id="beriHppSelectedIds">
                    <button type="submit" class="btn btn-warning">Ya, Hitung Ulang HPP</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="uploadModalShopee" tabindex="-1" aria-labelledby="uploadModalShopeeLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('adminshopee') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalShopeeLabel">Upload File Data Shopee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="file">Pilih File (XLSX/XLS/CSV)</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(action) {
        var form = document.getElementById('deleteForm');
        form.action = action;
        $('#confirmDeleteModal').modal('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const exportCheckboxes = document.querySelectorAll('.export-checkbox');
        const setWithoutHppButton = document.getElementById('setWithoutHppButton');

        selectAllCheckbox.addEventListener('change', function() {
            exportCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        exportCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    const allChecked = Array.from(exportCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });

        // Event listener untuk tombol "Tanpa HPP"
        setWithoutHppButton.addEventListener('click', function(e) {
            e.preventDefault();

            const selectedIds = Array.from(exportCheckboxes)
                                            .filter(checkbox => checkbox.checked)
                                            .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                alert('Pilih setidaknya satu transaksi untuk diatur HPP-nya.');
                return;
            }

            // Set nilai hidden input di modal
            document.getElementById('withoutHppSelectedIds').value = JSON.stringify(selectedIds);

            // Tampilkan modal konfirmasi
            $('#confirmWithoutHppModal').modal('show');
        });
    });


const beriHppButton = document.getElementById('beriHppButton');

beriHppButton.addEventListener('click', function (e) {
    e.preventDefault();

    const selectedIds = Array.from(document.querySelectorAll('.export-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedIds.length === 0) {
        alert('Pilih setidaknya satu transaksi untuk dihitung ulang HPP-nya.');
        return;
    }

    document.getElementById('beriHppSelectedIds').value = JSON.stringify(selectedIds);
    $('#confirmBeriHppModal').modal('show');
});


    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left',
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
</script>



@endsection