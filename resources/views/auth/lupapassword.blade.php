<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>

    <!-- Bootstrap & Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

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
</head>
<body style="background-color: #00518d;">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
        
        <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
    
    Silakan isi username Anda. Jika cocok, Anda bisa mengatur ulang password Anda.
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


        <form method="POST" action="{{ route('password.check') }}">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person-fill" style="color:#00518d; margin-right: 5px;"></i>Username
                </label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukan Username Anda" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Check</button>
            <a href="{{ route('login') }}" class="btn btn-secondary w-100 mt-2">Kembali</a>
        </form>

    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>
