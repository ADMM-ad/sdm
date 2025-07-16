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
    <form action="{{ route('penjualan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card card-primary">
            <div class="card-header bg-success text-white">
                <h3 class="card-title"> <i class="fas fa-file-invoice-dollar text-white mr-2"></i>Input Penjualan</h3>
            </div>
            <div class="card-body">
                <!-- Data Penjualan -->
                <div class="row">
                    <div class="col-md-6">
    <label>Metode Pengiriman</label>
    <select name="metode_pengiriman" class="form-control" required>
        <option value="">Pilih Metode</option>
        <option value="COD">COD</option>
        <option value="TRANSFER">TRANSFER</option>
        <option value="SHOPEE">SHOPEE</option>
    </select>
</div>
<div class="col-md-6 ">
    <label>Metode Pembayaran</label>
    <select name="metode_pembayaran" class="form-control">
        <option value="">Pilih Metode</option>
        <option value="dp">DP</option>
        <option value="lunas">Lunas</option>
    </select>
</div>

                    <div class="col-md-6">
                        <label>Nama Pembeli</label>
                        <input type="text" name="nama_pembeli" class="form-control" required>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Kode Pos</label>
                        <input type="text" name="kodepos" id="kodepos" class="form-control" required>
                    </div>
                   <div class="col-md-6 mt-2">
    <label>Provinsi</label>
    <input type="text" name="provinsi" id="provinsi" class="form-control" required>
</div>
<div class="col-md-6 mt-2">
    <label>Kota</label>
    <input type="text" name="kota" id="kota" class="form-control" required>
</div>
<div class="col-md-6 mt-2">
    <label>Kecamatan</label>
    <input type="text" name="kecamatan" id="kecamatan" class="form-control" required>
</div>
<div class="col-md-6 mt-2">
    <label>Wilayah</label>
    <input type="text" name="wilayah" id="wilayah" class="form-control" required>
</div>


                    <div class="col-md-12 mt-2">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6 mt-2">
    <label>Pilih Ekspedisi</label>
 <select name="kurir" id="kurir" class="form-control" required>
        <option value="">Pilih Ekspedisi</option>
        <option value="JNE">JNE</option>
        <option value="JT">J&T</option>
        <option value="SiCepat">SiCepat</option>
        <option value="iDexpress">IDExpress</option>
        <option value="Ninja">Ninja</option>
    </select>
</div>

                    <div class="col-md-6 mt-2">
                        <label>Ongkir</label>
                        <input type="number" name="ongkir" class="form-control" required>
                    </div>
                    <div class="col-md-6 mt-2">
    <label>Jumlah DP (jika ada)</label>
    <input type="number" name="dp" class="form-control" placeholder="Isi jika metode pembayaran DP">
</div>
                    <div class="col-md-6 mt-2">
                        <label>Bukti (opsional)</label>
                        <input type="file" name="bukti" class="form-control">
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Detail</label>
                        <textarea name="detail" class="form-control" rows="2"></textarea>
                    </div>
                </div>


                <hr>
      
   
            
      <div class="table-responsive">
                <table class="table  table-bordered " id="produk-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Detail Produk</th>
                            <th>Total</th>
                            <th>
                                <button type="button" class="btn btn-sm btn-success" id="addRow">Tambah</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="produk_id[]" class="form-control produk-select" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="jumlah[]" class="form-control jumlah-input" required></td>
                            <td><input type="text" class="form-control detail-produk" readonly></td>
                            <td><input type="text" class="form-control total-per-produk" readonly></td>
                            <td><button type="button" class="btn btn-sm btn-danger removeRow">Hapus</button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Bayar:</strong></td>
                            <td colspan="2">
                                <input type="text" id="totalBayar" name="total_bayar_display" class="form-control">
<input type="hidden" id="totalBayarHidden" name="total_bayar">

                            </td>
                        </tr>
                    </tfoot>
                </table>
</div>


  
                <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
            </div>
        </div>
        <input type="hidden" name="destination_id" id="destination_id">

    </form>
</div>
@endsection

@push('scriptsdua')
<script>
    // Data produk (id => detail_produk dan id => harga_jual)
    const produkData = @json($produk->pluck('detail_produk', 'id'));
    const hargaData = @json($produk->pluck('harga_jual', 'id'));

    // Fungsi untuk menghitung total per produk dan total semua produk + ongkir
    function hitungTotal() {
    let total = 0;

    document.querySelectorAll('#produk-table tbody tr').forEach(function (row) {
        const select = row.querySelector('.produk-select');
        const jumlahInput = row.querySelector('.jumlah-input');
        const totalInput = row.querySelector('.total-per-produk');
        const produkId = select.value;
        const harga = parseFloat(hargaData[produkId] || 0);
        const jumlah = parseFloat(jumlahInput?.value || 0);
        const subtotal = harga * jumlah;

        if (totalInput) {
            totalInput.value = subtotal ? subtotal.toLocaleString() : '';
        }

        total += subtotal;
    });

    const ongkir = parseFloat(document.querySelector('input[name="ongkir"]').value || 0);
    const totalBayar = total + ongkir;

    const totalInput = document.getElementById('totalBayar');
    const totalHidden = document.getElementById('totalBayarHidden');

    if (!totalInput.dataset.manualEdit) {
        totalInput.value = totalBayar.toLocaleString();
        totalHidden.value = totalBayar;
    }
}

document.getElementById('totalBayar').addEventListener('input', function () {
    this.dataset.manualEdit = true;

    const rawValue = this.value.replace(/\D/g, ''); // hapus titik/koma
    document.getElementById('totalBayarHidden').value = parseInt(rawValue || 0);
});


function initializeChoices() {
    document.querySelectorAll('.produk-select').forEach(function (select, index) {
        if (!select.classList.contains('choices-initialized')) {
            new Choices(select, {
                searchEnabled: true,
                itemSelectText: '',
                position: 'bottom',
            });
            select.classList.add('choices-initialized');
        }
    });
}


    // Tambahkan baris baru
    document.getElementById('addRow').addEventListener('click', function () {
        const row = `
        <tr>
            <td>
                <select name="produk_id[]" class="form-control produk-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($produk as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" name="jumlah[]" class="form-control jumlah-input" required></td>
            <td><input type="text" class="form-control detail-produk" readonly></td>
            <td><input type="text" class="form-control total-per-produk" readonly></td>
            <td><button type="button" class="btn btn-sm btn-danger removeRow">Hapus</button></td>
        </tr>
        `;
        document.querySelector('#produk-table tbody').insertAdjacentHTML('beforeend', row);
          initializeChoices();
    });

    // Hapus baris
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
            hitungTotal();
        }
    });

    // Update detail dan total saat produk dipilih atau jumlah diubah
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('produk-select')) {
            const row = e.target.closest('tr');
            const selectedId = e.target.value;
            row.querySelector('.detail-produk').value = produkData[selectedId] || '';
            hitungTotal();
        }

        if (e.target.name === 'ongkir') {
            hitungTotal();
        }
    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah-input')) {
            hitungTotal();
        }
    });



function updateChoices(id) {
        if (window.choicesInstances && window.choicesInstances[id]) {
            window.choicesInstances[id].destroy();
            window.choicesInstances[id] = new Choices(`#${id}`, {
                searchEnabled: true,
                itemSelectText: '',
            });
        }
    }

   ['provinsi', 'kota', 'kecamatan', 'wilayah'].forEach(id => {
    document.getElementById(id).addEventListener('input', function () {
        const provinsi = document.getElementById('provinsi').value.trim();
        const kota = document.getElementById('kota').value.trim();
        const kecamatan = document.getElementById('kecamatan').value.trim();
        const wilayah = document.getElementById('wilayah').value.trim();

        if (provinsi && kota && kecamatan && wilayah) {
            const keyword = `${provinsi} ${kota} ${kecamatan} ${wilayah}`;
            
            fetch(`/proxy/mengantar-address?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.json())
                .then(result => {
                    if (result.success && result.data.length > 0) {
                        const data = result.data[0];
                        const kodeposInput = document.getElementById('kodepos');
if (!kodeposInput.dataset.manualEdit) {
    kodeposInput.value = data.ZIP_CODE || '';
}

                        document.getElementById('destination_id').value = data._id;

                        // Reset ongkir jika kurir sudah dipilih
                        document.querySelector('input[name="ongkir"]').value = '';
                        const ekspedisi = document.getElementById('kurir').value;
                        if (ekspedisi) {
                            document.getElementById('kurir').dispatchEvent(new Event('change'));
                        }
                    } else {
                       
                    }
                });
        }
    });
});


document.getElementById('kurir').addEventListener('change', function () {
    const ekspedisi = this.value;
    const destinationId = document.getElementById('destination_id').value;

    if (!destinationId) {
        alert('Mohon pilih wilayah terlebih dahulu.');
        return;
    }

    if (!ekspedisi) {
        return;
    }

    const originId = '5fc63405f8f44b34aa4c4f9a'; // Tetap
    const url = `/proxy/mengantar-estimate?origin_id=${originId}&destination_id=${destinationId}&weight=1&COD_AMOUNT=0`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            const ongkirInput = document.querySelector('input[name="ongkir"]');
            const ekspedisiKey = ekspedisi;

            if (data.success && data.data?.[ekspedisiKey]) {
                const ekspedisiData = data.data[ekspedisiKey];
                const finalOngkir = ekspedisiData.price || 0;
                ongkirInput.value = finalOngkir;
                ongkirInput.dispatchEvent(new Event('input'));
                hitungTotal();
            } else {
                alert('Data ongkir untuk ekspedisi tersebut tidak tersedia.');
            }
        })
        .catch(err => {
            console.error('Gagal mengambil ongkir:', err);
        });
});

window.addEventListener('DOMContentLoaded', function () {
    const destinationId = document.getElementById('destination_id').value;
    const ekspedisiSelect = document.getElementById('kurir');

    // Jalankan otomatis hanya jika destinationId sudah tersedia
    if (destinationId && ekspedisiSelect.value) {
        ekspedisiSelect.dispatchEvent(new Event('change'));
    }
});


</script>
@endpush
