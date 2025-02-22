@extends('layouts.master')

@section('title', 'Edit User')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

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
            padding: 6px;
            cursor: text;
            border-radius: 22px;
        }
    </style>


    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                @php
                    $name = Session::get('userID');
                    $name = DB::table('UserDemographic')
                        ->where('UserID', $name->UserID)
                        ->first();
                @endphp
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-user" aria-hidden="true"></i>
                    <span class="ml-3">Edit User - {{ $name->FirstName . ' ' . $name->LastName }}</span>
                </h5>
            </div>

            <form {{-- action="{{ url('/update/user') }}" method="post" --}} id="myForm">
                {{-- @csrf --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div id="message"></div>
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
                    </div>
                @endif

                <div class="m-3">

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="row ml-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">First Name</label>
                                        <input hidden type="text" name="user_id" id="user_id"
                                               value="{{ $data->UserID }}">
                                        <input maxlength="25" type="text" class="form-control"
                                               onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"
                                               value="{{ $demographics->FirstName }}" name="first_name" id="first_name"
                                               required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Last Name</label>
                                        <input maxlength="25" type="text" class="form-control"
                                               onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"
                                               value="{{ $demographics->LastName }}" name="last_name" id="last_name"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>Allow Admin Control</label>
                                        @if ($id == $name->UserID)

                                            @if ($data->AdminAccess == '1')
                                                <input type="checkbox" disabled checked name="allow_login"
                                                       id="allow_login">
                                            @else
                                                <input type="checkbox" disabled name="allow_login" id="allow_login">
                                            @endif
                                        @else
                                            @if ($data->AdminAccess == '1')
                                                <input type="checkbox" checked name="allow_login" id="allow_login">
                                            @else
                                                <input type="checkbox" name="allow_login" id="allow_login">
                                            @endif
                                        @endif
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
                                        <input type="email" value="{{ $data->Email }}" class="form-control email"
                                               name="email" id="email" required autocomplete="off">
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
                                        <input type="text" value="{{ $phone->PhonePrimary }}" class="form-control"
                                               name="phone" id="phone">
                                    </div>
                                </div>
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
                                        <input type="text" class="form-control" name="password" id="password"
                                               minlength="6" maxlength="12" placeholder="******" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>User is Active</label>
                                        @if ($userAccont)
                                            @if ($userAccont->UserID == $name->UserID)
                                                @php
                                                    $disableCheckbox = true;
                                                @endphp
                                            @else
                                                @php
                                                    $disableCheckbox = false;
                                                @endphp
                                            @endif
                                            <input type="checkbox" <?php echo $disableCheckbox ? 'disabled' : ''; ?>
                                            @if ($userAccont->UserID == $id) @else
                                                @disabled(true)
                                            @endif checked
                                                   name="record_status" id="record_status">
                                        @else
                                            <input type="checkbox" name="record_status" id="record_status">
                                        @endif
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
                                                style="height: 49px !important; border-radius: 36px;"
                                                style="border-radius: 36px;" name="location1[]" id="location1">
                                            @foreach ($locationsUsers as $Index => $items)
                                                @php
                                                    $userAccont = DB::table('UserAccount')
                                                        // ->where('AccountID', $items->AccountID)
                                                        ->where('LocationID', $items->LocationID)
                                                        ->where('UserID', $data->UserID)
                                                        ->first();
                                                @endphp
                                                @if ($userAccont)
                                                    <option selected value="{{ $items->LocationID }}">
                                                        {{ $items->LocationName }}
                                                    </option>
                                                @else
                                                    <option value="{{ $items->LocationID }}">{{ $items->LocationName }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div hidden class="col-lg-6">
                                    <div class="form-group">
                                        <label for="">Other Viewable Accounts</label> <br>
                                        <select class="form-control locationAcc" multiple
                                                style="height: 49px !important; border-radius: 36px;"
                                                style="border-radius: 36px;" name="locationAcc[]" id="locationAcc">
                                            @foreach ($accounts->unique('AccountName') as $items)
                                                <option selected value="{{ $items->AccountID }}">
                                                    {{ $items->AccountName }}
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
                                        <select class="form-control" name="location" id="location" required>
                                            @foreach ($locationsUsers->unique('LocationName') as $items)
                                                @if ($data->LocationID == $items->LocationID)
                                                    <option selected value="{{ $items->LocationID }}">
                                                        {{ $items->LocationName }}
                                                    </option>
                                                @else
                                                    <option value="{{ $items->LocationID }}">{{ $items->LocationName }}
                                                    </option>
                                                @endif
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
                            <input type="submit" class="btn btn-secondary ml-4 mr-4" value="Update">
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>


    <script>
        window.addEventListener('DOMContentLoaded', function () {
            var phoneInput = document.getElementById('phone');
            var phoneNumber = phoneInput.value;
            var formattedNumber = removeCountryCode(phoneNumber);
            phoneInput.value = formattedNumber;
        });

        function removeCountryCode(phoneNumber) {
            // Remove all non-digit characters
            var cleaned = phoneNumber.replace(/\D/g, '');

            // Remove country code if present
            if (cleaned.length >= 10) {
                cleaned = cleaned.substring(cleaned.length - 10);
            }

            // Apply the desired phone number format
            var pattern = /(\d{3})(\d{3})(\d{4})/;
            var formattedNumber = cleaned.replace(pattern, '$1-$2-$3');

            return formattedNumber;
        }


        $('#myForm').submit(function (event) {
            $('#errorLocation1').text("")
            $('#errorEmail').text("")

            event.preventDefault();
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            var user_id = $('#user_id').val();
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
                url: '/update/user',
                type: 'POST',
                data: {
                    first_name: first_name,
                    last_name: last_name,
                    user_id: user_id,
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
                    $('#message').html("");
                    if (response.status == 'success') {
                        var table = '<div class="alert alert-success">';
                        table += response.message;
                        table += "</div>";
                        $('#message').append(table);
                        $('#password').val('');
                        window.location.href = '/floteq-user-admin';
                    } else if (response.status == 'notexists' || response.status == 'error') {
                        var table = '<div class="alert alert-warning">';
                        table += response.message;
                        table += "</div>";
                        $('#message').append(table);
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
                    $('#password-error').text('Password is too short. Minimum length: ' + passwordInput
                        .prop('minLength'));
                } else if (passwordValidity.tooLong) {
                    passwordInput.addClass('is-invalid');
                    $('#password-error').text('Password is too long. Maximum length: ' + passwordInput.prop(
                        'maxLength'));
                } else {
                    passwordInput.removeClass('is-invalid');
                    $('#password-error').text('');
                }
            });
        });

        $(document).ready(function () {


            $('#password').on('input', function () {
                var x = document.getElementById("password");
                x.type = 'password';
            });

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
