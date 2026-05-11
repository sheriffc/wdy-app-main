@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('custom_js')
    @include('assets.datatables-js')
@endsection

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                @if(session('import_errors') && count(session('import_errors')) > 0)
                    <ul class="mb-0 mt-1">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <div class="text-center mb-3"><h3>Manage Learners</h3></div>

                    @if(Auth::user()->user_type_id >= 80)
                    <div class="d-flex gap-2 mb-3 align-items-start flex-wrap">
                        <a href="{{ route('manage.learners.template') }}" class="btn btn-sm btn-outline-secondary">
                            <span class="fas fa-download me-1"></span> Download Upload Template
                        </a>

                        <form method="POST" action="{{ route('manage.learners.upload') }}" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                            @csrf
                            <input type="file" name="file" accept=".xlsx,.xls" class="form-control form-control-sm" style="max-width:280px" required>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="fas fa-upload me-1"></span> Upload Learners
                            </button>
                        </form>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <table id="dt-learners" data-csrf="{{ csrf_token() }}" class="table table-sm small-font" style="width:100%">
                        <thead>
                        <tr>
                            <th>Learner UID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>NIN</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $('#dt-learners').DataTable({
        ajax: {
            url: "{{ route('manage.learners.list') }}",
            type: "GET",
        },
        serverSide: true,
        processing: true,
        columns: [
            { data: 'learner_id', defaultContent: '' },
            { data: 'first_name', defaultContent: '' },
            { data: 'middle_name', defaultContent: '' },
            { data: 'last_name', defaultContent: '' },
            { data: 'sex_oid', defaultContent: '' },
            { data: 'date_of_birth', defaultContent: '' },
            { data: 'nin', defaultContent: '' },
            { data: 'created_at', defaultContent: '' },
        ],
        order: [[3, 'asc'], [1, 'asc']],
    });
</script>
@endsection
