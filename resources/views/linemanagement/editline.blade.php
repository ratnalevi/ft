@extends('layouts.master')

@section('title', 'Edit Line Management')
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

            <form action="{{ url('/update/device') }}" method="post">

                <input type="hidden" name="hidden_deviceline" id="hidden_deviceline"
                       @if (isset($data->DeviceLinesID)) value="{{ $data->DeviceLinesID }}" @endif>

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

                @csrf
                <div class="m-3 mb-5">


                    <div class="row">
                        <div class="col-lg-4">
                            <label for="location" class="ml-2">Location</label>
                            <select disabled style="border-radius: 36px;" class="form-control" name=""
                                    id="ddlFruits">
                                <option
                                    value="{{ $locationNDDD->LocationID }}">{{ $locationNDDD->LocationName }}</option>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <input type="hidden" name="selected_location" id="selected_location"
                                   value="{{ $locationNDDD->LocationID }}">
                            <input type="hidden" name="selected_device" id="selected_device"
                                   value="{{ $data->DevicesID }}">
                            <label for="devices" class="ml-2">Device</label>
                            <select disabled class="form-control devices" style="border-radius: 36px;"
                                    name="device_name"
                                    id="mySelect" required>
                                <option value="{{ $locationND->DevicesID }}">{{ $locationND->Name }}</option>
                            </select>

                        </div>
                    </div>
                    <h2 class=" mt-5">Update a Line</h2>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <input type="hidden" name="line" id="line" value="{{ $data->Line }}">
                                <label for="" class="ml-2">Line</label>
                                <select disabled name="line-value" id="line-value" required class="form-control">
                                    @for ($line = 1; $line <= 24; $line++)
                                        @if ($data->Line == $line)
                                            <option selected value="{{ $line }}">{{ $line }}</option>
                                        @else
                                            <option value="{{ $line }}">{{ $line }}</option>
                                        @endif
                                    @endfor



                                    {{--				        @if ($data->Line == $item->Line)
                                            <option selected value="{{ $item->Line }}">{{ $item->Line }}</option>
                                        @else
                                            <option value="{{ $item->Line }}">{{ $item->Line }}</option>
                                        @endif
 --}}

                                </select>
                                {{-- <input type="number"@if (isset($data->Line)) value="{{ $data->Line }}" @endif
                                    name="line" id="line" required> --}}
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="form-group">
                                <label for="" class="ml-2">Brand</label>
                                <input type="hidden" name="selected_brand" id="selected_brand"
                                       @if (isset($selectedBrand)) value="{{ $selectedBrand }}" @endif>
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
                                <input type="hidden" name="selected_keg" id="selected_keg"
                                       @if (isset($selectedKeg)) value="{{ $selectedKeg }}" @endif>
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
                                <input type="hidden" name="selected_distributer" id="selected_distributer"
                                       @if (isset($selectedDistributer)) value="{{ $selectedDistributer }}" @endif>
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
                                       @if (isset($data->OptTemp)) value="{{ $data->OptTemp }}" @endif name="optTemp"
                                       id="optTemp" required>
                                <div id="messageOptimunTemp" style="color: red;"></div>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Alert Value</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" oninput="validateNumber2(this.value,'tempAlert')"
                                       onkeypress="return /[0-9.]/.test(event.key)" class="form-control"
                                       @if (isset($data->TempAlertValue)) value="{{ $data->TempAlertValue }}" @endif
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
                                       @if (isset($data->OptPressure)) value="{{ $data->OptPressure }}" @endif
                                       name="opt_pressure" id="opt_pressure" required>
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
                                       name="press_alert_value" value="{{ $data->PressAlertValue }}"
                                       id="press_alert_value"
                                       required>
                                <div id="pressureAlert" style="color: red;"></div>

                            </div>

                        </div>
                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Pressure Alert </label>
                                <input type="text" {{-- oninput="validateNumber2(this.value,'tempPresAlert')"
                                    onkeypress="return /[0-9.]/.test(event.key)" --}} class="form-control"
                                       @if (isset($data->TempPressAlert)) value="{{ $data->TempPressAlert }}" @endif
                                       name="temp_press_alert" id="temp_press_alert">
                                <div id="tempPresAlert" style="color: red;"></div>

                            </div>
                        </div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Temperature Pressure Alert Time Out</label>
                                <input type="text" {{-- oninput="validateNumber2(this.value,'tempAlertTime')"
                                    onkeypress="return /[0-9.]/.test(event.key)" --}} class="form-control"
                                       @if (isset($data->TempPressAlertTimeOut)) value="{{ $data->TempPressAlertTimeOut }}"
                                       @endif
                                       name="temp_press_alert_timeout" id="temp_press_alert_timeout">
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
                                       @if (isset($data->KegCost)) value="{{ $data->KegCost }}" @endif name="keg_cost"
                                       id="keg_cost" required>
                                <div id="Keycost" style="color: red;"></div>

                            </div>
                        </div>

                        <div class="col-lg-6" id="hide">
                            <div class="form-group">
                                <label for="" class="ml-2">Pressure</label> <span
                                    style="font-size: 10px;color: red;"></span>
                                <input type="text" {{-- oninput="validateNumber(this.value,'pressureID')"
                                    onkeypress="return /[0-9.]/.test(event.key)"  --}} class="form-control"
                                       @if (isset($data->Pressure)) value="{{ $data->Pressure }}" @endif
                                       name="pressure" id="pressure">
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
                                       @if (isset($data->LineLength)) value="{{ $data->LineLength }}" @endif
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
                                       placeholder="0.00" @if (isset($data->OZFactor)) value="{{ $data->OZFactor }}"
                                       @endif name="OZFactorDisabled" id="OZFactorDisabled" required>
                                <input type="hidden" @if (isset($data->OZFactor)) value="{{ $data->OZFactor }}"
                                       @endif name="OZFactor" id="OZFactor">
                                <input type="hidden" @if (isset($data->OZFactor)) value="{{ $data->OZFactor }}"
                                       @endif name="old_oz" id="old_oz">
                                <div id="OZ_Factor" style="color: red;"></div>

                            </div>
                        </div>
{{--                        <div class="col-lg-6">--}}
{{--                            <div class="form-group">--}}
{{--                                <label style="color: #ffffff" class="ml-2">.</label> <span--}}
{{--                                    style="font-size: 10px;color: red;"></span>--}}
{{--                                <input type="button" style="background-color: #64738F; color:#ffffff"--}}
{{--                                       value="Unlock" id="enableOz" data-toggle="modal"--}}
{{--                                       data-target="#passwordModal" class="form-control btn btn-secondary mr-4 ml-4">--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>

                    <div class="row">
                        <div style="text-align: right;" class="col-lg-12 mt-4 ml-3 mr-3">
                            {{-- <input type="reset" value="Cancel" class="btn btn-secondary ml-4"> --}}
                            <input type="submit" id="submit" value="Update" class="btn btn-secondary ml-4 mr-4">
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

        // A $( document ).ready() block.
        $(document).ready(function () {
            var selectedDevice = $("#selected_device").val();
            $('#mySelect option[value="' + selectedDevice + '"]').attr("selected", "selected");


            var selectedLocation = $("#selected_location").val();
            $('#location option[value="' + selectedLocation + '"]').attr("selected", "selected");

            var selectedBrand = $("#selected_brand").val();
            $('#brand option[value="' + selectedBrand + '"]').attr("selected", "selected");

            var kegType = $("#selected_keg").val();
            $('#keg_type option[value="' + kegType + '"]').attr("selected", "selected");

            var distributer = $("#selected_distributer").val();
            $('#distributor option[value="' + distributer + '"]').attr("selected", "selected");

        });
    </script>

    <script>
        $('#ddlFruits').on('change', function () {
            var location_id = $('#ddlFruits').val();

            $.ajax({
                url: "{{ url('/load/devices') }}/" + location_id,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    $('#mySelect').empty();
                    var mySelect = $('#mySelect');

                    for (var i = 0; i < dataList.devices.length; i++) {
                        $('#mySelect').append($('<option>', {
                            value: dataList.devices[i]['DevicesID'],
                            text: dataList.devices[i]['Name']
                        }));
                    }

                },
                error: function (textStatus, errorThrown) {
                    alert('something went wrong while loading devices against location');
                }
            });

            $.ajax({
                url: "{{ url('/load/devices') }}/" + location_id,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    $('#mySelect').empty();
                    var mySelect = $('#mySelect');

                    for (var i = 0; i < dataList.devices.length; i++) {
                        $('#mySelect').append($('<option>', {
                            value: dataList.devices[i]['DevicesID'],
                            text: dataList.devices[i]['Name']
                        }));
                    }

                    var device = $("#mySelect").val()
                    var daysfilter = $('#daysfilter').val();
                    loadData(device, daysfilter, 1);
                },
                error: function (textStatus, errorThrown) {
                    alert('something went wrong');
                }
            });

        });
    </script>

@endsection
