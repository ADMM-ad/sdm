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
    <div class="card card-primary card-outline  p-3" style="border-color: #00518d;">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-id-card" style="color: #00518d"></i>
                Profil Anda
            </h4>
        </div>
        <div class="card-body">
           <div class="row mb-2">
    <div class="col-4 col-md-3 fw-bold">Nama : </div>
    <div class="col-8 col-md-9">{{ $user->name }}</div>
</div>
<div class="row mb-2">
    <div class="col-4 col-md-3 fw-bold">Username : </div>
    <div class="col-8 col-md-9">{{ $user->username }}</div>
</div>
<div class="row mb-2">
    <div class="col-4 col-md-3 fw-bold">Jabatan : </div>
    <div class="col-8 col-md-9">{{ $user->role }}</div>
</div>
        </div>
        <div class="text-end">
            <a href="{{ route('profil.edit') }}" class="btn btn-warning" >
                <i class="fas fa-edit"></i> Update
            </a>
        </div>
    </div>

</div>
@endsection
