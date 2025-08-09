<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<style>
     .card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideUp 1s ease-out;
        }
    .btn-primary {
        background-color: #00518d;
        border-color: #00518d;
    }

    .btn-primary:hover {
        background-color: #0A6ABF;
        border-color: #0A6ABF;
    }
    @keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<body style="background-color: #00518d;">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
        <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    
    Silahkan mengatur ulang password Anda
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('password.update', ['username' => $username]) }}" method="POST">
            @csrf
           <div class="mb-3">
    <label class="form-label">
        <i class="bi bi-lock-fill" style="color:#00518d; margin-right: 5px;"></i>Password Baru
    </label>
    <div class="input-group">
        <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan Password Baru" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
            <i class="bi bi-eye-slash"></i>
        </button>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">
        <i class="bi bi-lock-fill" style="color:#00518d; margin-right: 5px;"></i>Konfirmasi Password
    </label>
    <div class="input-group">
        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Masukan Ulang Password" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
            <i class="bi bi-eye-slash"></i>
        </button>
    </div>
</div>

            <button type="submit" class="btn btn-primary w-100">Simpan Password Baru</button>
            <a href="{{ route('login') }}" class="btn btn-secondary w-100 mt-1">Kembali</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });

    document.getElementById('togglePasswordConfirmation').addEventListener('click', function () {
        const passwordField = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    });
</script>

</html>
