@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Application Awaiting Review') }}</div>

                <div class="card-body">
                    {{ __('An administrator has been notified to review your application and may be in contact with you.  You will be informed via phone through the district office if your application is successful.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
