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


    {{-- Kartu Profil --}}
    <div class="card card-primary card-outline mt-3 mb-3 p-3" style="border-color: #31beb4;">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-id-card" style="color: #31beb4"></i>
                Profil Anda
            </h4>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Nama</div>
                <div class="col-md-9">{{ $user->name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Username</div>
                <div class="col-md-9">{{ $user->username }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-3 fw-bold">Role</div>
                <div class="col-md-9">{{ $user->role }}</div>
            </div>
        </div>
        <div class="text-end">
            <a href="{{ route('profil.edit') }}" class="btn" style="background-color : #1DCD9F">
                <i class="fas fa-edit"></i> Edit Profil
            </a>
        </div>
    </div>

</div>
@endsection
