@extends('layouts.master')

@section('title', 'Add Line Management')
@section('content')

    <style>
        #hide {
            display: none;
        }

        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        .form-control {
            border-radius: 20px;
        }

        .table th,
        .table td {
            padding: 12.75px;
            vertical-align: top;
            border-top: 1px solid #dde2ef;
        }

        /* input.btn.btn-secondary.ml-4 {
                                                                padding: 9px 41px;
                                                                border-radius: 21px;
                                                            } */
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
                    <i class="fa fa-users" aria-hidden="true"></i>
                    <span class="ml-3">Line Management</span>
                </h5>
            </div>

            <form action="{{ url('/save/device') }}" method="post">


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
                <div class="alert alert-danger" id="lineExistMessage" style="display: none;"></div>

                @csrf
                <div class="m-3 mb-5">


                    <div class="row">
                        <div class="col-lg-4">
                            <label for="location" class="ml-2">Location</label>
                            <select style="border-radius: 36px;" class="form-control" name="" id="ddlFruits">
                                @foreach ($locations->unique('LocationName') as $location)
                                    @if ($selectedLocation == $location->LocationID)
                                        <option value="{{ $location->LocationID }}"
                                                selected>{{ $location->LocationName }}
                                        </option>
                                    @else
                                        <option
                                            value="{{ $location->LocationID }}">{{ $location->LocationName }}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                        <div class="col-lg-4">
                            <label for="devices" class="ml-2">Devices</label>
                            <select class="form-control devices" style="border-radius: 36px;" name="device_name"
                                    id="mySelect" required>
                                @foreach ($devices as $items)
                                    @if ($selectedDevice == $items->DevicesID)
                                        <option value="{{ $items->DevicesID }}" selected>{{ $items->Name }}</option>
                                    @else
                                        <option value="{{ $items->DevicesID }}">{{ $items->Name }}</option>
                                    @endif
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <h2 class=" mt-5">Add a Line</h2>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Line</label>
                                <select onchange="checkLine()" class="form-control" name="line" id="line" required>
                                    @for ($line = 1; $line <= 24; $line++)
                                        <option value="{{ $line }}">{{ $line }}</option>
                                    @endfor
                                </select>
                                {{-- <input type="number" onkeypress="return /[0-9]/i.test(event.key)" class="form-control"
                                    placeholder="2" name="line" id="line" required> --}}
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">Brand</label>
                                <select name="brand" id="brand" class="form-control" required>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->BeerBrandsID }}">{{ $brand->Brand }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Keg Type</label>
                                <select name="keg_type" id="keg_type" class="form-control" required>
                                    @foreach ($kegtypes as $kegtype)
                                        <option value="{{ $kegtype->KegTypeID }}">{{ $kegtype->KeyName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">Distributor</label>
                                <select name="distributor" id="distributor" class="form-control" required>
                                    @foreach ($distributers as $distributer)
                                        <option value="{{ $distributer->DistributorID }}">{{ $distributer->DistName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Optimum Temperature (F)</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber(this.value,'messageOptimunTemp')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="000.00"
                                       name="optTemp" id="optTemp" required>
                                <div id="messageOptimunTemp" style="color: red;"></div>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Alert Value</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber2(this.value,'tempAlert')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="0.00"
                                       name="temp_alert_value" id="temp_alert_value" required>
                                <div id="tempAlert" style="color: red;"></div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Optimum Pressure</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber(this.value,'messageOptimunPres')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="000.00" name="opt_pressure" id="opt_pressure" required>
                                <div id="messageOptimunPres" style="color: red;"></div>

                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">Pressure Alert Value</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber2(this.value,'pressureAlert')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="0.00"
                                       name="press_alert_value" id="press_alert_value" required>
                                <div id="pressureAlert" style="color: red;"></div>

                            </div>

                        </div>
                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Pressure Alert </label>
                                <input type="text"
                                       {{-- oninput="validateNumber2(this.value,'tempPresAlert')" --}} {{-- onkeypress="return /[0-9.]/.test(event.key)"  --}} class="form-control"
                                       placeholder="0.00" name="temp_press_alert" id="temp_press_alert">
                                <div id="tempPresAlert" style="color: red;"></div>

                            </div>
                        </div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Pressure Alert Time Out</label>
                                <input type="text"
                                       {{-- oninput="validateNumber2(this.value,'tempAlertTime')" --}} {{-- onkeypress="return /[0-9.]/.test(event.key)" --}} class="form-control"
                                       placeholder="0.00" name="temp_press_alert_timeout" id="temp_press_alert_timeout">

                                <div id="tempAlertTime" style="color: red;"></div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Keg Cost </label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber(this.value,'Keycost')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="000.00" name="keg_cost" id="keg_cost" required>

                                <div id="Keycost" style="color: red;"></div>
                            </div>

                        </div>

                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Pressure</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text"
                                       {{-- oninput="validateNumber(this.value,'pressureID')" --}} {{-- onkeypress="return /[0-9.]/.test(event.key)"  --}} class="form-control"
                                       max="999.99" placeholder="000.00" name="pressure" id="pressure">
                                <div id="pressureID" style="color: red;"></div>
                            </div>
                        </div>

                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">Line Length (feet)</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber2(this.value,'LineLength')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="0.00"
                                       name="line_length" id="line_length" required>

                                <div id="LineLength" style="color: red;"></div>

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">OZ Factor</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" onchange="swipeValue(this.value)"
                                       oninput="validateNumber(this.value,'OZ_Factor')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       placeholder="1.15"
                                       value="1.15" name="OZFactor" id="OZFactor">
{{--                                <input type="hidden" value="1.15" name="OZFactor" id="OZFactor">--}}
                                <div id="OZ_Factor" style="color: red;"></div>

                            </div>
                        </div>
{{--                        <div class="col-lg-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <label style="color: #ffffff" class="ml-2">.</label> <span--}}
{{--                                    style="font-size: 10px;color: red;"></span>--}}
{{--                                <input type="button"--}}
{{--                                       style="background-color: #64738F;--}}
{{--                                    color:rgb(253, 253, 253)"--}}
{{--                                       style="margin-top: 0px;" value="Unlock" id="enableOz" data-toggle="modal"--}}
{{--                                       data-target="#passwordModal" class="form-control btn btn-secondary mr-4 ml-4">--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>


                    <div class="row">
                        <div style="text-align: right;" class="col-lg-12 mt-4 ml-3 mr-3">
                            {{-- <input type="button" value="Enable OZ" id="enableOz" data-toggle="modal"
                                data-target="#passwordModal" class="btn btn-secondary ml-4"> --}}
                            <input type="submit" id="submit" value="Save" class="btn btn-secondary ml-4 mr-4">
                        </div>
                    </div>
                </div>

            </form>


        </div>
    </div>
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Enter Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="password" class="form-control" id="passwordInput" placeholder="Enter password">
                    <p class="text-danger" id="passwordError" style="display: none;">Wrong password. Try again.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" style="border-radius: 19px;" class="btn btn-secondary"
                            data-dismiss="modal">Close
                    </button>
                    <button type="button" style="border-radius: 19px;" class="btn btn-secondary"
                            id="verifyPassword">Verify Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("verifyPassword").addEventListener("click", function () {
            var enteredPassword = document.getElementById("passwordInput").value;
            var correctPassword = 'floteq@1234';

            if (enteredPassword === correctPassword) {
                document.getElementById("OZFactorDisabled").removeAttribute("disabled");
                $('#passwordModal').modal('hide');
                document.getElementById("enableOz").disabled = true;
            } else {
                document.getElementById("passwordError").style.display = "block";
                document.getElementById("enableOz").disabled = false;
            }
        });

        function swipeValue(oz) {
            var ozValue = $('#OZFactorDisabled').val();
            document.getElementById("OZFactor").value = ozValue;
            var ozValue = $('#OZFactor').val();
        }

        function checkLine() {
            var deviceID = $('#mySelect').val();
            var line = $('#line').val();
            $.ajax({
                url: "{{ url('/check/line') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    deviceID: deviceID,
                    line: line,
                },
                success: function (response) {
                    if (response === "exist") {
                        $('#lineExistMessage').text('This line already exists. Please choose a different one.')
                            .show();
                        $('#submit').prop('disabled', true);
                    } else {
                        $('#lineExistMessage').hide();
                        $('#submit').prop('disabled', false);
                    }
                },
                // error: function(textStatus, errorThrown) {
                //     alert('sometfhing went wrong while loading devices against location');
                // }
            });
        }
    </script>
    <script>
        function validateNumber(value, id) {
            var number = parseFloat(value); // Parse the input value as a floating-point number
            var errorMessage = document.getElementById(id);

            if (isNaN(number) || number > 999.99 || number < 1) {
                errorMessage.textContent = 'Please enter a number between 1 and 999.99';
                $('#submit').prop('disabled', true);
            } else {
                errorMessage.textContent = '';
                $('#submit').prop('disabled', false);
            }
        }


        function validateNumber2(value, id) {
            var number = parseFloat(value); // Parse the input value as a floating-point number
            var errorMessage = document.getElementById(id);

            if (isNaN(number) || number > 10000 || number < 1) {
                errorMessage.textContent = 'Please enter a number between 1 and 10000';
                $('#submit').prop('disabled', true);
            } else {
                errorMessage.textContent = '';
                $('#submit').prop('disabled', false);
            }
        }

        ajaxcall();


        function ajaxcall() {
            var location_id = $('#ddlFruits').val();

            $.ajax({
                url: "{{ url('/load/devices') }}/" + location_id,
                type: 'GET',
                dataType: 'json',
                data: data = {
                    'addLine': "addLine",
                },
                success: function (dataList) {
                    $('#mySelect').empty();
                    var mySelect = $('#mySelect');

                    console.log(dataList);


                    dataList.forEach(element => {
                        $('#mySelect').append($('<option>', {
                            value: element.DevicesID,
                            text: element.Name,
                        }));
                    });

                },
                complete: function (dataComplete) {
                    var DeviceID = $('#mySelect').val();
                    deviceLine(DeviceID);
                    checkLine();
                },
                error: function (textStatus, errorThrown) {
                    alert('something went wrong while loading devices against location');
                }
            });
        }

        function deviceLine(DeviceID) {
            $.ajax({
                url: "{{ url('/get/devices/lines') }}/" + DeviceID,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    /*
                    console.log(dataList);
                    $('#line').empty();

                    dataList.forEach(element => {
                        $('#line').append($('<option>', {
                            value: element.Line,
                            text: element.Line,
                        }));
                    });
                    */
                },
            });
        }

        $('#ddlFruits').on('change', function () {
            ajaxcall()
        });
        $('#mySelect').on('change', function () {
            var DeviceID = $('#mySelect').val();
            deviceLine(DeviceID);
            checkLine();
        })
    </script>

@endsection
