<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cek Ongkir Mengantar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h4>Cek Ongkir via Mengantar</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ url('/cek-ongkir-mengantar') }}">
        @csrf
        <div class="mb-3">
            <label>Alamat Asal (misal: Campurejo Kediri)</label>
            <input type="text" name="origin" class="form-control" value="{{ old('origin', $origin_input ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label>Alamat Tujuan (misal: Jakarta Selatan)</label>
            <input type="text" name="destination" class="form-control" value="{{ old('destination', $destination_input ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label>Nominal COD (boleh 0)</label>
            <input type="number" name="cod" class="form-control" value="0">
        </div>
        <button class="btn btn-success">Cek Ongkir</button>
    </form>

    @isset($data['data'])
        <hr>
        <h5>Hasil Estimasi Ongkir</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kurir</th>
                    <th>Harga</th>
                    <th>Diskon</th>
                    <th>Total Estimasi</th>
                    <th>COD Fee</th>
                    <th>Estimasi Hari</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['data'] as $kurir => $item)
                    <tr>
                        <td>{{ $kurir }}</td>
                        <td>Rp{{ number_format($item['price'] ?? 0) }}</td>
                        <td>{{ $item['discountPercent'] ?? 0 }}%</td>
                        <td>Rp{{ number_format($item['estimatedSpecialPrice'] ?? 0) }}</td>
                        <td>{{ $item['codFee'] ?? 0 }}</td>
                        <td>{{ $item['estimatedDate'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endisset
</div>
</body>
</html>
