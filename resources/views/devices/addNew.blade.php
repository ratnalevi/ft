@extends('layouts.master')

@section('title', 'Add New Device')
@section('content')

    <style>
        .fa,
        .fas {
            font-weight: 900;
            font-size: 22px;
        }

        .form-control {
            border-radius: 36px;
        }
    </style>

    <div class="col-lg-12">
        <div class="card m-3">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span class="ml-3">Device Management </span>
                    {{-- <a class="m-3" href="#"> <i class="fa fa-plus" aria-hidden="true"></i></a> --}}
                </h5>
            </div>

            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                    @php
                        Session::forget('success');
                    @endphp
                </div>
            @endif
            @if ($errors->any())
                <div id="auto-hide-alert" class="alert alert-danger alert-dismissible fade show" role="alert">

                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
            @endif


            <form action="/save-Devimanagement" method="POST">
                @csrf
                <div class="m-3">

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Account Owner</label>
                                <select name="accountName" id="accountName" onchange="getLocation(this.value)"
                                        class="form-control">
                                    <option value="">Select Account</option> <!-- Default option -->
                                    @foreach ($accounts as $item)
                                        <option value="{{ $item->AccountID }}">{{ $item->AccountName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Location</label>
                                <select name="locationName" id="locationName" class="form-control">
                                    <option value="">Select a location</option> <!-- Default option -->
                                    @foreach ($locations as $item)
                                        <option value="{{ $item->LocationID }}">{{ $item->LocationName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="text-align: center;" class="col-lg-4">
                            <div class="form-group">
                                <label for="">
                                    Reporting Intervals (default every 10 sec)
                                </label>
                                <div class="spain11 d-flex">
                                    <input style="height: 19px;" type="checkbox" class="form-control mt-2" id="input"
                                           name="input">

                                    <input name="time" style="margin: 0px 11px;" value="11:00" type="time"
                                           class="form-control" id="time">
                                    <select name="seconds" id="seconds" class="form-control">
                                        <option value="5">5 Sec</option>
                                        <option value="10">10 Sec</option>
                                        <option value="15">15 Sec</option>
                                        <option value="10">30 Sec</option>
                                        <option value="1">1 Mint</option>
                                        <option value="10">10 Mint</option>
                                        <option value="15">15 Mint</option>
                                        <option value="30">30 Mint</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Device Name</label>
                                <input type="text" minlength="6" maxlength="64" name="deviceName"
                                       onkeypress="return /[0-9a-zA-Z\s!@#$^&*()_+\=\[\]{}|\\:;\',<.>?~`\/\-]/i.test(event.key) && event.key !== '%'"
                                       id="deviceName" class="form-control deviceName" required>
                            </div>

                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">Device S/N</label>
                                <input type="text" minlength="6" maxlength="12" {{ old('deviceSerial') }}
                                onkeypress="return /[0-9]/i.test(event.key)" class="deviceSerial form-control"
                                       name="deviceSerial" id="deviceSerial" required>
                                <span id="serial-error" style="color: red;"></span>
                            </div>
                        </div>
                        <div style="text-align: center;" class="col-lg-4">
                            <div class="form-group">
                                <label style="color: white;" for="">
                                    Reporting Intervals (default every 10 sec)
                                </label>
                                <div class="spain11 d-flex">
                                    <input style="height: 19px;" type="checkbox" class="form-control mt-2" id="input1"
                                           name="input1">
                                    <input name="time1" style="margin: 0px 11px;" value="11:00" type="time"
                                           class="form-control" id="time1">
                                    <select name="seconds1" id="seconds1" class="form-control">
                                        <option value="5">5 Sec</option>
                                        <option value="10">10 Sec</option>
                                        <option value="15">15 Sec</option>
                                        <option value="10">30 Sec</option>
                                        <option value="1">1 Mint</option>
                                        <option value="10">10 Mint</option>
                                        <option value="15">15 Mint</option>
                                        <option value="30">30 Mint</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">WIFI SSID (Network Name)</label>
                                <input type="text" minlength="6" maxlength="16" class="wifiSSID form-control"
                                       name="wifiSSID" id="wifiSSID">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="">WIFI Password</label>
                                <input type="text" minlength="6" maxlength="16" class="wifiPASS form-control"
                                       name="wifiPASS" id="wifiPASS">
                            </div>
                        </div>
                        <div style="text-align: center;" class="col-lg-4">
                            <div class="form-group">
                                <label style="color: white;" for="">
                                    Reporting Intervals (default every 10 sec)
                                </label>
                                <div class="spain11 d-flex">
                                    <input name="input2" style="height: 19px;" type="checkbox"
                                           class="form-control mt-2" id="input2">
                                    <input name="time2" style="margin: 0px 11px;" value="11:00" type="time"
                                           class="form-control" id="time2">
                                    <select name="seconds2" id="seconds2" class="form-control">
                                        <option value="5">5 Sec</option>
                                        <option value="10">10 Sec</option>
                                        <option value="15">15 Sec</option>
                                        <option value="10">30 Sec</option>
                                        <option value="1">1 Mint</option>
                                        <option value="10">10 Mint</option>
                                        <option value="15">15 Mint</option>
                                        <option value="30">30 Mint</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid rgb(167, 167, 167)">

                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label for="">Azure Host Path</label>
                                <input type="text" maxlength="64" class="azurehost form-control" name="azurehost"
                                       id="azurehost">
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label for="">Azure Device ID</label>
                                <input type="text" maxlength="64" disabled class="azureDevice form-control"
                                       name="azureDevice" id="azureDevice">
                            </div>
                        </div>
                        {{-- <div class="col-lg-4">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mt-5">
                                        <label>IoT Edge Device</label>
                                        <input type="checkbox" checked name="iotcheck" id="iotcheck">
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="">Azure Device Key</label>
                                <input type="text" maxlength="64" class="azurekey form-control" name="azurekey"
                                       id="azurekey">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label style="color: white" for="">Azure Device Key</label>
                                <button type="button" style="background: #a9a7a7; color: #b90000;  border-radius: 36px;"
                                        ;
                                        class="btn btn-primary">Generate Azure Connection Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr style="border-top: 1px solid rgb(167, 167, 167)">

                    <div class="row">
                        <div class="col-10"></div>
                        {{-- <div style="text-align: right;" class="col-lg-12">
                            <button type="reset" style="background: #a9a7a7; border-radius: 36px; padding: 14px 50px;"
                                class="btn btn-primary">Cancel</button>
                        </div> --}}
                        <div class="col-lg-2">
                            <div class="form-group">
                                <input type="submit" class="btn btn-secondary ml-4 mr-4" value="Save" id="submitBtn">
                            </div>
                            {{-- <button type="submit" style="background: #a9a7a7; border-radius: 36px; padding: 14px 50px;"
                                class="btn btn-primary">Save</button> --}}
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $("#auto-hide-alert").alert('close');
            }, 5000);
        });

        $('#deviceSerial').on('change', function () {
            var serial = $(this).val();

            $.ajax({
                url: "{{ route('checkSerial') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    serial: serial
                },
                success: function (response) {
                    if (response.status === 'error') {
                        // Serial is already in use, display an error message and disable the submit button
                        $('#serial-error').text(response.message);
                        $('#submitBtn').prop('disabled', true);
                    } else {
                        // Serial is unique, clear the error message and enable the submit button
                        $('#serial-error').text('');
                        $('#submitBtn').prop('disabled', false);
                    }
                }
            });
        });

        function getLocation(id) {
            $.ajax({
                type: 'get',
                url: '/getLocation/Devimanagement/' + id,
                success: function (data) {
                    // Clear existing options
                    $('#locationName').empty();

                    // Add new options based on the retrieved data
                    data.forEach(function (item) {
                        $('#locationName').append('<option value="' + item.LocationID + '">' + item
                            .LocationName + '</option>');
                    });
                },
                error: function (error) {
                    console.log('Error fetching locations:', error);
                }
            });

        }

        $(document).ready(function () {
            function callValidation() {
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
            }

            $('#wifiSSID').on('keyup', function () {
                callValidation()
            });
            $('#wifiPASS').on('keyup', function () {
                callValidation()
            });
            $('#deviceSerial').on('keyup', function () {
                callValidation()
            });
            $('#deviceName').on('keyup', function () {
                callValidation()
            });
        });
    </script>

@endsection
