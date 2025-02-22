<!DOCTYPE html>
<html lang="en">

<head>

    <style>
        .ht-40 {
            height: 63px;
        }
    </style>

    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }
    </style>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>


    <!-- Title -->
    <title> Floteq - @yield('title')</title>
    @csrf
    @include('layouts.styles')


</head>

<body class="main-body app sidebar-mini">


<!-- Loader -->
<div id="global-loader">
    <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->

<!-- Page -->
<div class="page">

    @include('layouts.sidebar')

    <!-- main-content -->
    <div class="main-content app-content">
        @include('layouts.header')

        <!-- container -->
        <div class="container-fluid">

            <!-- breadcrumb -->
            {{-- <div class="breadcrumb-header justify-content-between">
                <div class="left-content">
                    <div>
                        <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, welcome back!</h2>
                        <p class="mg-b-0">Sales monitoring dashboard template.</p>
                    </div>
                </div>
                <div class="main-dashboard-header-right">
                    <div>
                        <label class="tx-13">Customer Ratings</label>
                        <div class="main-star">
                            <i class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i
                                class="typcn typcn-star active"></i> <i class="typcn typcn-star active"></i> <i
                                class="typcn typcn-star"></i> <span>(14,873)</span>
                        </div>
                    </div>
                    <div>
                        <label class="tx-13">Online Sales</label>
                        <h5>563,275</h5>
                    </div>
                    <div>
                        <label class="tx-13">Offline Sales</label>
                        <h5>783,675</h5>
                    </div>
                </div>
            </div> --}}
            <!-- /breadcrumb -->

            <!-- row -->
            <div class="row row-sm mt-4 m-3">
                @yield('content')
            </div>
            <!-- row closed -->

        </div>
        <!-- /Container -->
    </div>
    <!-- /main-content -->

    @include('layouts.footer')
</div>
<!-- End Page -->

<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>

@include('layouts.script')
</body>

</html>
