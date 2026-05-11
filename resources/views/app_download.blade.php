@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
                    <div class="text-center">
                        <p>Latest App Version: {{ $versionDetails->version_code }} </p>
                        <div>
                            <a href="{{ asset("apks".DIRECTORY_SEPARATOR.$versionDetails->filename) }}" class="btn btn-primary">Download</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
