{{--<nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #DFF8FF;">--}}
{{--<nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #E7FAFF;">--}}
<nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #F1FCFF;">
{{--    E7FFF2    F9FFFC    --}}
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <div class="mb-2">
            <img src="{{Vite::asset('resources/images/wideya_logo_horiz_blue_green.svg')}}" alt="wideya logo" width="150">
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto ">
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('/') ? 'active' : '' ) }}"  href="{{ route('landing') }}">{{ __('Summary') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('school-list') ? 'active' : '' ) }}" href="{{ route('school-list') }}">{{ __('School List') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('report-monitoring') ? 'active' : '' ) }}" href="{{ route('report-monitoring') }}">{{ __('Report Monitoring') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('teacher-reports') ? 'active' : '' ) }}" href="{{ route('teacher-reports') }}">{{ __('Teacher Reports') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('learner-reports') ? 'active' : '' ) }}" href="{{ route('learner-reports') }}">{{ __('Learner Reports') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('faqs') ? 'active' : '' ) }}" href="{{ route('faqs') }}">{{ __('FAQs') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{(Request::is('about') ? 'active' : '' ) }}" href="{{ route('about') }}">{{ __('About') }}</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link {{(Request::is('login') ? 'active' : '' ) }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link {{(Request::is('register') ? 'active' : '' ) }}" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <h6 class="dropdown-header">Permission Level: {{DB::table('user_type')->where('type_id', Auth::user()->user_type_id)->value('type_name')}}</h6>
                            <hr class="dropdown-divider">

                            @if(Auth::user()->user_type_id == 999)
                            <h6 class="dropdown-header">Super Admin</h6>
                            <a class="dropdown-item" href="{{ route('manage.academic-years') }}">{{ __('Academic Years') }}</a>
                            <a class="dropdown-item" href="{{ route('manage.district-offices') }}">{{ __('District Offices') }}</a>
                            <a class="dropdown-item" href="{{ route('manage.scope-groups') }}">{{ __('Scope Groups') }}</a>
                            <a class="dropdown-item" href="{{ route('view.trace') }}">{{ __('Stack Trace Reports') }}</a>
                            <hr class="dropdown-divider">
                            @endif

                            <a class="dropdown-item" href="{{ route('manage.learners') }}">{{ __('Learners') }}</a>
                            @if(Auth::user()->user_type_id >= 80)
                            <h6 class="dropdown-header">Admin</h6>
                            <a class="dropdown-item" href="{{ route('manage.schools') }}">{{ __('Schools') }}</a>
                            @endif
                            @if(Auth::user()->user_type_id >= 40)
                            <a class="dropdown-item" href="{{ route('manage.users') }}">{{ __('Users') }}</a>
                            <a class="dropdown-item" href="{{ route('manage.mobile-password-reset') }}">
                                {{ __('Mobile Password Resets') }}
                                @php
                                $userType = Auth::user()->user_type_id;
                                $pwrCount = DB::scalar("SELECT count(*) from android_password_resets apr INNER JOIN user u ON apr.username = u.username AND u.active WHERE u.user_type_id < $userType AND apr.status='pending'");
                                @endphp
                                @if($pwrCount)
                                    <span class="badge rounded-pill bg-secondary">{{$pwrCount}}</span>
                                @endif
                            </a>
                            <hr class="dropdown-divider">
                            @endif

                            @if(Auth::user()->user_type_id >= 20)
                                <a class="dropdown-item" href="{{ route('app-download') }}">{{ __('Android App Download') }}</a>
                                <hr class="dropdown-divider">
                            @endif

                            <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

@php
    $noticeDate = '';
    if(isset($todayDate)){
        $noticeDate = $todayDate;
    }else{
        $noticeDate = date("Y-m-d");
    }
    $DashboardNotices = DB::table('dashboard_notices')
                        ->where('date_from', '<=',$noticeDate)
                        ->where('date_to', '>=',$noticeDate)
                        ->whereOr('override', '=',1)
                        ->orderBy('display_order')
                        ->get();
@endphp
@if ($DashboardNotices)
    @foreach ($DashboardNotices as $notice)
        <div class="container mt-3 mb-0 alert alert-{{($notice->display_class) ? $notice->display_class : 'secondary'}} text-center" role="alert">
            {!! ($notice->display_text) ? str_replace("todayDate",date("Y-m-d"),$notice->display_text) : '' !!}
        </div>
    @endforeach
@endif
