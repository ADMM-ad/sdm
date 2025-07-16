@extends('masterlayout')

@section('content')
<div class="container mt-3">
    <h2>Pilih Produk</h2>

    {{-- Form Tambah Produk --}}
    <form id="form-tambah-produk">
        @csrf
        <input type="hidden" name="id_penjualan" value="{{ $id }}">

        <div class="row mb-3">
            <div class="col">
                <select name="id_produk" class="form-control" required>
                    <option value="">Pilih Produk</option>
                    @foreach ($produk as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_produk }} - Rp{{ number_format($p->harga_jual) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required min="1">
            </div>
            <div class="col">
                <button type="submit" class="btn btn-success">Tambah Produk</button>
            </div>
        </div>
    </form>

    {{-- Tabel Daftar Produk dalam Card --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart mr-1" style="color: #1DCD9F;"></i> Produk Yang Dibeli
                    </h3>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered text-nowrap" id="tabel-produk">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td>{{ $item->produk->nama_produk }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>Rp{{ number_format($item->total_harga) }}</td>
                                    <td><button class="btn btn-danger btn-sm btn-hapus">Hapus</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="text-end mt-3">
        <a href="{{ route('penjualan.create') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

{{-- JavaScript --}}
<script>
document.getElementById('form-tambah-produk').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('{{ route("penjualan.tambah_produk") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const row = `<tr data-id="${data.detail.id}">
                <td>${data.produk.nama_produk}</td>
                <td>${data.detail.jumlah}</td>
                <td>Rp${Number(data.detail.total_harga).toLocaleString()}</td>
                <td><button class="btn btn-danger btn-sm btn-hapus">Hapus</button></td>
            </tr>`;

            document.querySelector('#tabel-produk tbody').insertAdjacentHTML('beforeend', row);
            document.querySelector('[name=id_produk]').selectedIndex = 0;
            document.querySelector('[name=jumlah]').value = '';
        }
    });
});

document.querySelector('#tabel-produk').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-hapus')) {
        const row = e.target.closest('tr');
        const id = row.dataset.id;

        fetch('/penjualan/hapus-produk/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                row.remove();
            }
        });
    }
});
</script>
@endsection
