@extends('layouts.master')

@section('title', 'Add User')
@section('content')
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" --}}
    {{-- integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <style>
        a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        #chart {
            margin: 0 auto;
        }

        g#SvgjsG1055 {
            display: none;
        }

        .choices[data-type*=select-multiple] .choices__inner,
        .choices[data-type*=text] .choices__inner {
            padding-top: 5px;
            cursor: text;
            border-radius: 22px;
        }

        .choices {
            position: relative;
            margin-bottom: 1px;
            font-size: 16px;
        }

        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>

    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                @php
                    $name = Session::get('userID');
                    $name = DB::table('UserDemographic')
                        ->where('UserID', $name->UserID)
                        ->first();
                @endphp
                <h5>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <span class="ml-3">New User - {{ $name->FirstName . ' ' . $name->LastName }}</span>
                </h5>
            </div>

            <div id="message"></div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <form {{-- action="{{ url('/save/user') }}" method="post" --}} id="myForm">
                {{-- @csrf --}}

                <div class="m-3">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">First Name</label>
                                        <input maxlength="25" type="text" class="form-control" id="first_name"
                                               name="first_name" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"
                                               required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Last Name</label>
                                        <input maxlength="25" type="text" class="form-control" id="last_name"
                                               name="last_name" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>Allow Admin
                                            Control</label>
                                        <input type="checkbox" name="allow_login" id="allow_login">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="">Email Address</label>
                                        <input type="email" id="email" class="form-control" name="email"
                                               autocomplete="off" required>
                                        <span style="color: red" id="errorEmail"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>Receive Email Alerts</label>
                                        <input type="checkbox" name="record_email" id="record_email">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Phone (Optional)</label>
                                        <input type="text" class="form-control" name="phone" autocomplete="off"
                                               id="phone">
                                    </div>
                                </div>
                                <div hidden class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">ConfigurationID </label>
                                        <input type="text" class="form-control" name="ConfigurationID"
                                               id="ConfigurationID">
                                    </div>
                                </div>
                            </div>
                            <div class="row ml-4">


                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>Receive Text
                                            Alerts</label>
                                        <input type="checkbox" name="record_text" id="record_text">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Password</label>
                                        <input type="password" minlength="6" maxlength="12" class="form-control"
                                               name="password" autocomplete="new-password" id="password" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>User is Active</label>
                                        <input type="checkbox" checked name="record_status" id="record_status">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for=""> Viewable Locations</label>
                                        <select class="form-control locationLOca" multiple
                                                style="height: 49px !important; border-radius: 36px; padding: 11px 0px 0px 0px;"
                                                name="location1[]" id="location1">
                                            @foreach ($locationsUsers->unique('LocationName') as $items)
                                                <option value="{{ $items->LocationID }}">{{ $items->LocationName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span style="color: red" id="errorLocation1"></span>
                                    </div>
                                </div>
                                <div hidden class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Other Viewable Accounts</label> <br>
                                        <select class="form-control locationAcc" multiple
                                                style="height: 49px !important; border-radius: 36px; padding: 11px 0px 0px 0px;"
                                                style="border-radius: 36px;" name="locationAcc[]" id="locationAcc">
                                            @foreach ($accounts->unique('AccountName') as $items)
                                                <option value="{{ $items->AccountID }}">{{ $items->AccountName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for=""> Default Location at Login</label>
                                        <select required class="form-control" name="location" id="location">
                                            @foreach ($locationsUsers->unique('LocationName') as $items)
                                                <option value="{{ $items->LocationID }}">{{ $items->LocationName }}
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div style="text-align: right;" class="col-lg-12 mt-4 ml-3 mr-3"> --}}
                        {{-- <input type="reset" value="Cancel" class="btn btn-secondary ml-4"> --}}
                        {{-- <input type="submit" value="Save" class="btn btn-secondary ml-4 mr-4"> --}}
                        {{-- </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-secondary ml-4 mr-4" value="Save">
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>


    <script>
        var input = document.getElementById("first_name");
        input.addEventListener("first_name", function () {
            if (this.value.length < 3) {
                this.setCustomValidity("Minimum length is 3 characters.");
            } else if (this.value.length > 25) {
                this.setCustomValidity("Maximum length is 25 characters.");
            } else {
                this.setCustomValidity("");
            }
        });

        var input = document.getElementById("last_name");
        input.addEventListener("last_name", function () {
            if (this.value.length < 3) {
                this.setCustomValidity("Minimum length is 3 characters.");
            } else if (this.value.length > 15) {
                this.setCustomValidity("Maximum length is 25 characters.");
            } else {
                this.setCustomValidity("");
            }
        });


        $('#myForm').submit(function (event) {
            $('#errorLocation1').text("")
            $('#errorEmail').text("")

            event.preventDefault();
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var allow_login = $('#allow_login').prop('checked');
            var email = $('#email').val();
            var record_email = $('#record_email').prop('checked');
            var phone = $('#phone').val();
            // var ConfigurationID = $('#ConfigurationID ').val();
            var record_text = $('#record_text').prop('checked');
            var password = $('#password').val();
            var record_status = $('#record_status').prop('checked');
            var location = $('#location').val();
            var location1 = $('#location1 option:selected').toArray().map(item => item.value).join();
            console.log("----" + location1);
            var locationAcc = $('#locationAcc option:selected').toArray().map(item => item.value).join();


            // Make AJAX request
            $.ajax({
                url: '/save/user',
                type: 'POST',
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    allow_login: allow_login,
                    email: email,
                    record_email: record_email,
                    phone: phone,
                    // ConfigurationID: ConfigurationID,
                    record_text: record_text,
                    password: password,
                    record_status: record_status,
                    location: location,
                    location1: location1,
                    locationAcc: locationAcc,
                },
                success: function (response) {
                    console.log(response);
                    if (response.location1 == 'The location1 field is required.') {
                        $('#errorLocation1').text("Viewable locations required")
                    } else if (response.email == 'The email field is required.') {
                        $('#errorEmail').text("Email field required")

                    } else if (response.email == 'The email has already been taken.') {
                        $('#errorEmail').text("Email already exists")

                    } else if (response.status == 'add') {
                        $('#message').html("");
                        var table = '<div class="alert alert-success">';
                        table += "User added successfully";
                        table += "</div>";
                        $('#message').append(table);
                        $('#errorLocationDefault').html('');
                        $("#myForm").trigger("reset");
                        window.location.href = '/floteq-user-admin';
                    }
                }
            });
        });


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
        });

        function formatPhoneNumber(input) {
            const phoneNumber = input.value;
            const formattedNumber = libphonenumber.formatPhoneNumber(phoneNumber, 'US', 'National');
            input.value = formattedNumber;
        }

        $(document).ready(function () {


            var multipleCancelButton = new Choices('#location1', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });
            var multipleCancelButton = new Choices('#locationAcc', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });
        });
    </script>
@endsection
