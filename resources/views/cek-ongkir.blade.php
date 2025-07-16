<!DOCTYPE html>
<html>
<head>
    <title>Cek Ongkir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h2>Cek Ongkir JNE, TIKI, POS</h2>

    <form id="ongkirForm">
        <label>Provinsi Asal:</label>
        <select id="province_origin" name="province_origin">
            <option>Pilih Provinsi</option>
            @foreach ($provinces as $prov)
                <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
            @endforeach
        </select>

        <label>Kota Asal:</label>
        <select id="city_origin" name="origin"></select>

        <label>Provinsi Tujuan:</label>
        <select id="province_destination" name="province_destination">
            <option>Pilih Provinsi</option>
            @foreach ($provinces as $prov)
                <option value="{{ $prov['province_id'] }}">{{ $prov['province'] }}</option>
            @endforeach
        </select>

        <label>Kota Tujuan:</label>
        <select id="city_destination" name="destination"></select>

        <label>Kurir:</label>
        <select name="courier" id="courier">
            <option value="jne">JNE</option>
            <option value="tiki">TIKI</option>
            <option value="pos">POS</option>
        </select>

        <button type="submit">Cek Ongkir</button>
    </form>

    <div id="result"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // CSRF setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#province_origin').change(function() {
            $.post('/get-cities', { province_id: $(this).val() }, function(data) {
                $('#city_origin').empty().append('<option>Pilih Kota</option>');
                data.forEach(function(city) {
                    $('#city_origin').append(`<option value="${city.city_id}">${city.city_name}</option>`);
                });
            });
        });

        $('#province_destination').change(function() {
            $.post('/get-cities', { province_id: $(this).val() }, function(data) {
                $('#city_destination').empty().append('<option>Pilih Kota</option>');
                data.forEach(function(city) {
                    $('#city_destination').append(`<option value="${city.city_id}">${city.city_name}</option>`);
                });
            });
        });

        $('#ongkirForm').submit(function(e) {
            e.preventDefault();
            $.post('/check-ongkir', $(this).serialize(), function(data) {
                let result = '<h3>Hasil Ongkir:</h3><ul>';
                data.forEach(function(service) {
                    result += `<li>${service.service}: Rp ${service.cost[0].value} - Estimasi ${service.cost[0].etd} hari</li>`;
                });
                result += '</ul>';
                $('#result').html(result);
            });
        });
    </script>
</body>
</html>
