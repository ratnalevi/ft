<style>
    .side-menu .side-menu__icon {
        font-size: 23px;
        margin-right: 14px;
        width: 29px;
        height: 31px;
        line-height: 34px;
        border-radius: 3px;
        text-align: center;
        color: #a8b1c7;
        fill: #5b6e88;
    }
</style>

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    $name = Session::get('userID');
    $user = DB::table('UserAccount')
        ->where('UserID', $name->UserID)
        ->select('AdminAccess')
        ->where('AdminAccess', '0')
        ->first();
    $userAdmin = DB::table('UserAccount')
        ->where('UserID', $name->UserID)
        ->select('AdminAccess')
        ->where('AdminAccess', '1')
        ->first();
@endphp

    <!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">

    <div class="main-sidemenu">
        <ul class="side-menu">
            <li class="slide ml-4">
                <a href="{{ url('/home') }}"><img style="max-width: 85%;"
                                                  src="{{ asset('assets/img/brand/logo.png') }}"></a>
            </li>
            <li class="slide mt-4">
                <a class="side-menu__item" href="{{ url('/home') }}">
                            <span class="side-menu__icon">
                                <i class="fa fa-database" aria-hidden="true"></i>
                            </span>
                    <span class="side-menu__label ">Dashboard (Home)</span>
                </a>
            </li>


            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                            <span class="side-menu__icon">
                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                            </span>
                    <span class="side-menu__label">User Reporting</span><i class="angle fe fe-chevron-down"></i>
                </a>

                <ul class="slide-menu">
                    {{-- <li><a class="slide-item" href="{{ url('/sensor-reporting') }}">Sensor Reports</a></li> --}}
                    <li><a class="slide-item" href="{{ url('/line-reporting') }}">Sensor Reports</a></li>
                    {{-- <li><a class="slide-item" href="{{ url('/line-reportingPres') }}">Line Pressure Reports</a></li>
                    <li><a class="slide-item" href="{{ url('/line-reportingTDS') }}">Line TDS Reports</a></li> --}}
                    <li><a class="slide-item" href="{{ url('/brand-comparison') }}">Brand Comparison</a></li>
                    <li><a class="slide-item" href="{{ url('/pourscore-reports') }}">PourScore Reports</a></li>
                    <li><a class="slide-item" href="{{ url('/pour-score-detail') }}">PourScore Details</a></li>
                    <li><a class="slide-item" href="{{ url('/trend-analysis') }}">Trend Analysis</a></li>
                    <li><a class="slide-item" href="{{ url('/in-range-reports') }}">InRange Reports</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ url('/alert-center') }}">
                            <span class="side-menu__icon">
                                <i class="fa fa-bell" aria-hidden="true"></i>
                            </span>
                    <span class="side-menu__label">Alert Center</span>
                </a>
            </li>
            @if ($userAdmin)
                <li class="slide">

                    <a class="side-menu__item" data-toggle="slide" href="#">
                                <span class="side-menu__icon">
                                    <i class="fa fa-cogs" aria-hidden="true"></i>
                                </span>
                        <span class="side-menu__label">Administration</span><i
                            class="angle fe fe-chevron-down"></i>
                    </a>

                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ url('/floteq-user-admin') }}">User Management</a>
                        </li>
                        <li><a class="slide-item" href="{{ url('/line-management') }}">Line Management</a></li>
                    </ul>
                </li>
            @endif

            @if ($name->UserID == '44' || $name->UserID == '89')
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                                <span class="side-menu__icon">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </span>
                        <span class="side-menu__label">Floteq Admin</span><i
                            class="angle fe fe-chevron-down"></i>
                    </a>

                    <ul class="slide-menu">
                        {{-- <li><a class="slide-item" href="{{ url('/floteq-user-admin') }}">Accounts</a>
                        </li> --}}
                        <li><a class="slide-item" href="{{ url('/accounts') }}">Accounts</a></li>
                        <li><a class="slide-item" href="{{ url('/alert-management') }}">Alerts</a></li>
                        <li><a class="slide-item" href="{{ url('/brand-management') }}">Brands</a></li>
                        <li><a class="slide-item" href="{{ url('/device-management') }}">Devices</a></li>
                        <li><a class="slide-item" href="{{ url('/location-management') }}">Locations</a></li>
                        <li><a class="slide-item" href="{{ url('/PosItem') }}">POS Items</a></li>
                    </ul>
                </li>
            @endif

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#">
                            <span class="side-menu__icon">
                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                            </span>
                    <span class="side-menu__label">Documentation</span><i class="angle fe fe-chevron-down"></i>
                </a>

                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('/documentation') }}">User Guide</a></li>
                    <li><a class="slide-item" href="{{ url('/documentationUsage') }}">Content Usage License</a>
                    </li>
                    <li><a class="slide-item" href="{{ url('/documentationPolicy') }}">Privacy Policy</a></li>
                    <li><a class="slide-item" href="{{ url('/documentationSaas') }}">Saas License</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ url('/logout') }}">
                            <span class="side-menu__icon">
                                <i class="fas fa-sign-out-alt"></i>
                            </span>
                    <span class="side-menu__label">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
