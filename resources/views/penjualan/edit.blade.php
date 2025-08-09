@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card card-primary card-outline " style="border-color: #00518d;">
            <div class="card-header ">
                <h3 class="card-title"> <i class="fas fa-edit mr-2" style="color: #00518d;"></i>Edit Penjualan</h3>
            </div>
            <div class="card-body">
                <!-- Data Penjualan -->
                <div class="row">
                    <div class="col-md-6">
                        <label>Metode Pengiriman</label>
                        <select name="metode_pengiriman" class="form-control">
                            <option value="">Pilih Metode</option>
                            <option value="COD" {{ $penjualan->metode_pengiriman == 'COD' ? 'selected' : '' }}>COD</option>
                            <option value="TRANSFER" {{ $penjualan->metode_pengiriman == 'TRANSFER' ? 'selected' : '' }}>TRANSFER</option>
                            <option value="SHOPEE" {{ $penjualan->metode_pengiriman == 'SHOPEE' ? 'selected' : '' }}>SHOPEE</option>
                        </select>
                    </div>
 <div class="col-md-6">
    <label>Metode Pembayaran</label>
    <select name="metode_pembayaran" class="form-control">
        <option value="">Pilih Metode</option>
        <option value="dp" {{ $penjualan->metode_pembayaran == 'dp' ? 'selected' : '' }}>DP</option>
        <option value="lunas" {{ $penjualan->metode_pembayaran == 'lunas' ? 'selected' : '' }}>Lunas</option>
    </select>
</div>

                    <div class="col-md-6 mt-2">
                        <label>Nama Pembeli</label>
                        <input type="text" name="nama_pembeli" class="form-control" value="{{ $penjualan->nama_pembeli }}" required>
                    </div>
<div class="col-md-6 mt-2">
    <label>Nama CS</label>
    <select name="id_user" class="form-control" >
        <option value="">Pilih Customer Service</option>
        @foreach($customerServices as $cs)
            <option value="{{ $cs->id }}" {{ $penjualan->id_user == $cs->id ? 'selected' : '' }}>
                {{ $cs->name }}
            </option>
        @endforeach
    </select>
</div>

                    <div class="col-md-6 mt-2">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ $penjualan->no_hp }}" required>
                    </div>
                  <div class="col-md-6 mt-2">
    <label>Kode Pos</label>
    <input type="text" name="kodepos" id="kodepos" class="form-control" value="{{ old('kodepos', $penjualan->kodepos) }}">
</div>

<div class="col-md-6 mt-2">
    <label>Provinsi</label>
    <input type="text" name="provinsi" id="provinsi" class="form-control" value="{{ old('provinsi', $penjualan->provinsi) }}">
</div>
<div class="col-md-6 mt-2">
    <label>Kota</label>
    <input type="text" name="kota" id="kota" class="form-control" value="{{ old('kota', $penjualan->kota) }}">
</div>
<div class="col-md-6 mt-2">
    <label>Kecamatan</label>
    <input type="text" name="kecamatan" id="kecamatan" class="form-control" value="{{ old('kecamatan', $penjualan->kecamatan) }}">
</div>
<div class="col-md-6 mt-2">
    <label>Wilayah</label>
    <input type="text" name="wilayah" id="wilayah" class="form-control" value="{{ old('wilayah', $penjualan->wilayah) }}">
</div>

                    <div class="col-md-12 mt-2">
                        <label>Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" required>{{ $penjualan->alamat }}</textarea>
                    </div>
                    <div class="col-md-6 mt-2">
    <label>Pilih Ekspedisi</label>
    <select name="kurir" id="kurir" class="form-control" >
        <option value="">Pilih Ekspedisi</option>
        <option value="JNE" {{ $penjualan->kurir == 'JNE' ? 'selected' : '' }}>JNE</option>
        <option value="JT" {{ $penjualan->kurir == 'JT' ? 'selected' : '' }}>J&T</option>
        <option value="SiCepat" {{ $penjualan->kurir == 'SiCepat' ? 'selected' : '' }}>SiCepat</option>
        <option value="iDexpress" {{ $penjualan->kurir == 'iDexpress' ? 'selected' : '' }}>IDExpress</option>
        <option value="Ninja" {{ $penjualan->kurir == 'Ninja' ? 'selected' : '' }}>Ninja</option>
    </select>
</div>
                    <div class="col-md-6 mt-2">
                        <label>Ongkir</label>
                        <input type="number" name="ongkir" class="form-control" value="{{ $penjualan->ongkir }}" >
                    </div>
                    <div class="col-md-6 mt-2">
    <label>Jumlah DP (jika ada)</label>
    <input type="number" name="dp" class="form-control" value="{{ $penjualan->dp }}" placeholder="Isi jika metode pembayaran DP">
</div>

                    <div class="col-md-6 mt-2">
                        <label>Bukti (jika ingin ganti)</label>
                        <input type="file" name="bukti" class="form-control">
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2">{{ $penjualan->catatan }}</textarea>
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Detail</label>
                        <textarea name="detail" class="form-control" rows="2">{{ $penjualan->detail }}</textarea>
                    </div>
                    <div class="col-md-6 mt-2">
    <label>Order ID</label>
    <input type="text" name="order_id" class="form-control" value="{{ old('order_id', $penjualan->order_id) }}">
</div>

<div class="col-md-6 mt-2">
    <label>No Resi</label>
    <input type="text" name="no_resi" class="form-control" value="{{ old('no_resi', $penjualan->no_resi) }}">
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
                            <th><button type="button" class="btn btn-sm btn-success" id="addRow">Tambah</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->detailPenjualan as $detail)
                        <tr>
                            <td>
                                <select name="produk_id[]" class="form-control produk-select" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($produk as $p)
                                        <option value="{{ $p->id }}" {{ $p->id == $detail->id_produk ? 'selected' : '' }}>
                                            {{ $p->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="jumlah[]" class="form-control jumlah-input" value="{{ $detail->jumlah }}" required></td>
                            <td><input type="text" class="form-control detail-produk" value="{{ $detail->produk->detail_produk }}" readonly></td>
                            <td><input type="text" class="form-control total-per-produk" value="{{ number_format($detail->total_harga, 0, ',', '.') }}" readonly></td>
                            <td><button type="button" class="btn btn-sm btn-danger removeRow">Hapus</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total Bayar:</strong></td>
                            <td colspan="2">
                                <input type="text" id="totalBayar" name="total_bayar_visible" class="form-control" value="{{ number_format($penjualan->total_bayar, 0, ',', '.') }}">
<input type="hidden" id="totalBayarHidden" name="total_bayar" value="{{ $penjualan->total_bayar }}">

                            </td>
                        </tr>
                    </tfoot>
                </table>
</div>

@foreach(request()->query() as $key => $value)
    <input type="hidden" name="_filter[{{ $key }}]" value="{{ $value }}">
@endforeach
                <button type="submit" class="btn btn-warning mt-2"><i class="fas fa-edit"></i> Update</button>
                 @php
    $backRoute = auth()->user()->role === 'admin' ? route('penjualan.laporan') : route('penjualan.index');
@endphp

<a href="{{ url()->previous() }}" class="btn btn-secondary mt-2 ">
    <i class="fas fa-reply"></i> Kembali
</a>
            </div>
        </div>
        <input type="hidden" id="destination_id" name="destination_id" value="">
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
        document.getElementById('totalBayar').value = totalBayar.toLocaleString();
        document.getElementById('totalBayarHidden').value = totalBayar;
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

document.getElementById('totalBayar').addEventListener('input', function () {
    const val = this.value.replace(/\./g, '').replace(',', '.');
    const num = parseFloat(val);
    if (!isNaN(num)) {
        document.getElementById('totalBayarHidden').value = num;
    }
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

        // Reset ongkir dan kodepos saat ada perubahan
        document.querySelector('input[name="ongkir"]').value = '';
        document.getElementById('kodepos').value = '';
        document.getElementById('destination_id').value = '';

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

                        const ekspedisi = document.getElementById('kurir').value;
                        if (ekspedisi) {
                            document.getElementById('kurir').dispatchEvent(new Event('change'));
                        }
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

    const originId = '5fc63405f8f44b34aa4c4f9a';
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
                if (typeof hitungTotal === 'function') hitungTotal();
            } else {
                alert('Data ongkir untuk ekspedisi tersebut tidak tersedia.');
            }
        })
        .catch(err => {
            console.error('Gagal mengambil ongkir:', err);
        });
});


</script>
@endpush
