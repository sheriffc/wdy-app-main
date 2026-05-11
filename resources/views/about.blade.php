@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('content')

    <div class="container mt-2 d-flex justify-content-center">
        <div class="card col-md-9">
            <div class="card-body">
                <h3 class="card-title">About</h3>

                <p class="mt-3">
                    Wi De Ya (meaning <i>'We Are Here'</i> in Krio) is an app-based education attendance and enrolment monitoring system used to record daily teacher and pupil attendance,
                    pupil enrolment and school data in government and government-assisted schools in Sierra Leone.
                </p>
                <p>
                    The system supports the Teaching Service Commission (TSC) and Ministry of Basic and Secondary School Education (MBSSE) to make improvements
                    to education service delivery through the collection of near real-time pupil, teacher and school data.
                </p>
                <p>
                    Wi De Ya is led by TSC in collaboration with MBSSE, and supported by independent partner <a href="https://cgatechnologies.org.uk/">CGA Technologies</a> (CGA).
                    It is funded by the Multi Donor Trust Fund (World Bank, GPE, EU, Irish Aid and FCDO).
                </p>
                <p>
                    Wi De Ya is an important step in the Government of Sierra Leone’s (GoSL) commitment to improve education under the <span class="text-decoration-underline">Free Quality School Education</span> and <span class="text-decoration-underline">Radical Inclusion policies</span>.
                </p>
                <p>
                    Find out more about Wi De Ya in our <a  href="{{ route('faqs') }}">{{ __('FAQs') }}</a>
                </p>
            </div>
        </div>

    </div>

@endsection

