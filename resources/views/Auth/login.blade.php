<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        #spinner-div {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 70;
            left: 0;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 2;
        }
    </style>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords"
          content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>

    <!-- Title -->
    <title> Floteq - Login </title>
    @include('layouts.styles')

    <style>
        .main-signup-header h2 {
            font-weight: 500;
            color: #004E8A;
            letter-spacing: -1px;
            text-align: center;
        }

        h5.font-weight-semibold.mb-4 {
            text-align: center;
        }
    </style>
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
        <div class="row">
            <div class="col-lg-4"></div>
            <div class="col-lg-4">
                <!-- The content half -->
                <div class="login d-flex align-items-center py-2">
                    <!-- Demo content-->
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-8 col-lg-8 col-xl-9 mx-auto">
                                <div class="card-sigin">
                                    <div id="spinner-div" class="pt-5">
                                        <div class="mt-5">
                                            <div class="spinner-border text-primary" role="status">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <a href="/login">
                                            <img class="mx-auto d-block sign-favicon ht-159"
                                                 src="{{ asset('assets/img/brand/logo.png') }}" alt="logo">
                                        </a>
                                    </div>
                                    <div class="card-sigin">
                                        <div class="main-signup-header">
                                            @php
                                                $name = Session::get('userID');
                                            @endphp
                                            @if (!$name)
                                                <form>
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input style="margin-bottom: 7px;" class="form-control"
                                                               id="email" name="email"
                                                               placeholder="Enter your email" type="email">
                                                        <span class="mt-1" style="color: red;"
                                                              id="emailError"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Password</label>
                                                        <input class="form-control" minlength="6" maxlength="12"
                                                               id="password" name="password"
                                                               placeholder="Enter your password" type="password">
                                                    </div>

                                                    <div class="main-signin-footer mt-3">
                                                        {{-- <p><a href="">Forgot password?</a></p> --}}
                                                        {{-- <div class="row">
                                                            <div class="col-lg-6">
                                                                <p>Don't have an account?</p>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <p style="text-align: right">
                                                                    <a href="/register">Createe an account</a>
                                                                </p>
                                                            </div>
                                                        </div> --}}
                                                        <button id="login" type="button"
                                                                class="btn btn-main-primary btn-block mt-4">Login
                                                        </button>
                                                    </div>
                                                </form>
                                            @else
                                                <h2>Welcome back!</h2>
                                                <p style="text-align:center;">
                                                    <a style="text-decoration: underline;" href="/home">Click here
                                                        to continue</a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End -->
                </div>
                <!-- End -->

            </div>
            <div class="col-lg-4"></div>

        </div>
    </div>
</div>

@include('layouts.script')
<script>
    $(document).ready(function () {

        $('#password').on('keyup', function () {
            var passwordInput = $(this);
            var passwordValidity = passwordInput.prop('validity');
            if (passwordValidity.tooShort) {
                passwordInput.addClass('is-invalid');
                $('#password-error').text('Password is too short. Minimum length: ' +
                    passwordInput
                        .prop('minLength'));
            } else if (passwordValidity.tooLong) {
                passwordInput.addClass('is-invalid');
                $('#password-error').text('Password is too long. Maximum length: ' +
                    passwordInput.prop(
                        'maxLength'));
            } else {
                passwordInput.removeClass('is-invalid');
                $('#password-error').text('');
            }
        });

        $('#login').on('click', function () {
            $('#spinner-div').show();
            let route = "/login";
            let token = "{{ csrf_token() }}";
            $('#emailError').text("");

            $.ajax({
                url: route,
                type: 'POST',
                data: {
                    _token: token,
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function (response) {
                    console.log(response);
                    toastr.options.timeOut = 10000;
                    if (response.success) {
                        $('#spinner-div').show();
                        window.location.href = "/home";
                        toastr.success(response.success);
                    } else if (response.email) {
                        // toastr.success(response.email);
                        $('#emailError').text(response.email);
                        $('#spinner-div').hide();
                    } else {
                        toastr.error(response.error);
                        $('#spinner-div').hide();
                    }
                },
                complete: function (data) {
                    if (data.responseJSON.error) {
                        $('#spinner-div').hide();
                    } else if (data.responseJSON.success) {
                        $('#spinner-div').show();
                    }
                    console.log(data);
                }
            });
        });
    });
</script>
</body>

</html>
