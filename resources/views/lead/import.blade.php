@extends('masterlayout')

@section('content')
<div class="container mt-4">
    <h4><i class="fas fa-upload mr-2"></i>Import Data Lead</h4>

 @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
    <form action="{{ route('lead.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf
        <div class="form-group">
            <label for="file">Upload File Excel (.xlsx)</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Import</button>
        <a href="{{ route('lead.template') }}" class="btn btn-success mt-2">
    <i class="fas fa-download mr-1"></i> Download Template Excel
</a>

    </form>
</div>
@endsection
