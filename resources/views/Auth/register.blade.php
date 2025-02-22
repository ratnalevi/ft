<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
          content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>

    <!-- Title -->
    <title> Floteq - Register </title>
    @include('layouts.styles')
</head>

<body class="error-page1 main-body bg-light">

<!-- Loader -->
<div id="global-loader">
    <img src="{{ asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->

<!-- Page -->
<div class="page">
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <div class="login d-flex align-items-center py-2">
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                <div class="card-sigin">
                                    <div class="mb-5">
                                        <a href="/login">
                                            <img class="mx-auto d-block sign-favicon ht-159"
                                                 src="{{ asset('assets/img/brand/logo.png') }}" alt="logo">
                                        </a>
                                    </div>
                                    <div class="main-signup-header">

                                        @php
                                            $name = Session::get('userID');
                                        @endphp
                                        @if (!$name)
                                            <form action="#">
                                                <div class="form-group">
                                                    <label>Name</label> <input
                                                        class="form-control"
                                                        placeholder="Enter your Name"
                                                        type="text">
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label> <input class="form-control"
                                                                                placeholder="Enter your email"
                                                                                type="text">
                                                </div>
                                                <div class="form-group">
                                                    <label>Password</label> <input class="form-control"
                                                                                   placeholder="Enter your password"
                                                                                   type="password">

                                                    <div class="main-signup-footer mt-5">
                                                        <p>Already have an account? <a href="/login">Sign
                                                                In</a></p>
                                                    </div>
                                                </div>
                                                <button class="btn btn-main-primary btn-block">Create
                                                    Account
                                                </button>

                                            </form>
                                        @else
                                            <h2>Welcome back!</h2>
                                            <h5 class="font-weight-semibold mb-4">You are Already Login. </h5>
                                            <p>If You want to visit Dashboard <a href="/home">Click here</a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End -->
                </div>
            </div>
            <div class="col-lg-4"></div>

        </div>
    </div>
</div>
@include('layouts.script ')
</body>

</html>
