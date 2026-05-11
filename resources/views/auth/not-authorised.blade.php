@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Not Authorised') }}</div>

                <div class="card-body">
                    {{ __('You are not permitted to access the requested page.  If you wish to challenge this please contact a site administrator.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
