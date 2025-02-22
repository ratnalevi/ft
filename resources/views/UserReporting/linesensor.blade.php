@extends('layouts.master')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
@section('title', 'Sensor Report')
@section('content')
    <style>
        .choices[data-type*=select-multiple] .choices__inner,
        .choices[data-type*=text] .choices__inner {
            cursor: text;
            border-radius: 36x;
            border-radius: 36px;
        }

        .mt-5 {
            padding-top: 96px;
            padding-left: 96px;
        }

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

        .fa-refresh:before {
            content: "\f021";
            padding: 10px;
        }

        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        #chart-container {
            margin: 0 auto;
        }

        .form-control {
            font-size: 16px;
        }
    </style>

    <input type="hidden" name="last_device" id="last_device" value="{{ $lastDevice->DevicesID }}">



    @foreach ($allDevicesIds as $value)
        <input type="hidden" name="all_device_ids[]" id="all_device_ids" value="{{ $value }}">
    @endforeach

    <div class="col-lg-12">
        <div class="card">
            <div class="row m-3">
                <div class="col-lg-6 mt-3">
                    <h4>
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="ml-3">Select a location</span>

                    </h4>
                </div>

                @php
                    $names = Session::get('userID');
                    $name = DB::table('UserDemographic')
                        ->where('UserID', $names->UserID)
                        ->first();
                @endphp

                <div class="col-lg-6 mt-1">
                    <select class="form-control locationLOca"
                            style="border-radius: 36px; text-align: left;font-size: 14px;"
                            name="location33" id="LocationGet">
                        @foreach ($locationsUsers as $items)
                            @if ($items->LocationID == $name->LocationID)
                                <option value="{{ $items->AccountID }}" selected>{{ $items->LocationName }}</option>
                            @else
                                <option value="{{ $items->AccountID }}">{{ $items->LocationName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <div class="row">
                    <div class="col-lg-8">
                        <h5>
                            <i class="fa fa-compass" aria-hidden="true"></i>
                            <span class="ml-3">Sensor Reporting (Temperature, Pressure and TDS) by Date/Time</span>
                        </h5>
                    </div>
                    <div class="col-lg-4" style="padding: 0;">
                        <a id="refreshBtn" href="#">
                            <i class="fa fa-refresh" aria-hidden="true"
                               style="margin-top: 10%;margin-bottom: -10px; font-size: 100%;float: right; padding-right: 25px;">Refresh
                            </i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="m-3">
                {{-- <div class="row">
                    <div class="col-lg-2 mt-1">
                        <h4>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                            <span class="ml-2">Device:</span>
                        </h4>
                    </div>
                </div> --}}
                <div class="row" id="mySelect">
                    <div class="col-lg-6" id="mySelect1">
                        <div class="form-group" id="mySelect2">
                            <select id="beerbrand" name="beerbrand" class="form-control" placeholder="Select Devices"
                                    style="border-radius: 21px; height: 50px;font-size: 14px;" multiple>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <select class="form-control type" id="type"
                                    style="border-radius: 21px;height: 50px;font-size: 14px;">
                                <option value="temp">Temperature (F)</option>
                                <option value="pre">Pressure (PSI)</option>
                                <option value="tds">TDS</option>
                            </select>
                        </div>
                    </div>
                    @php
                        $timestamp = time();
                        $date90DaysAgo = strtotime('-15 days', $timestamp);
                        $dateString = date('Y-m-d\T06:00', $date90DaysAgo);
                        $currentDate = date('Y-m-d\T06:00');

                        $minDateTime = new DateTime();
                        $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                        $currentDateTime = new DateTime();
                        $maxDateString = $currentDateTime->format('Y-m-d\TH:i');
                    @endphp
                    <div class="col-lg-6">
                        <input style="margin: 0px 25px 0px 0px; height: 50px;font-size: 14px; border-radius: 21px;"
                               id="daysfilter" type="datetime-local" class="form-control" value="{{ $dateString }}"
                               min="{{ $minDateString }}" max="{{ $maxDateString }}">
                    </div>

                    <div class="col-lg-6">
                        <input style="border-radius: 21px;font-size: 14px; height: 50px;" type="datetime-local"
                               id="daysfilter2" class="form-control devicesfilter" value="{{ $currentDate }}"
                               min="{{ $minDateString }}" max="{{ $maxDateString }}">
                    </div>

                    {{-- <div class="col-lg-2 mt-2">
                        <a id="refreshBtn" href="#">
                            <i class="fa fa-refresh" aria-hidden="true"
                                style="margin-top: 3%; font-size: 100%;float: right;"> Refresh
                            </i>

                        </a>
                    </div> --}}

                </div>
                <div class="row mt-4">
                    <div id="spinner-div" class="pt-5">
                        <div class="mt-5">
                            <div class="spinner-border text-primary" role="status">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card mg-b-20">
                            <div class="card-body">
                                <div class="main-content-label mg-b-5 pt-2pb-5 text-center">
                                    <span class="mb-3" id="valuesTempData"></span>
                                </div>
                                <div id="chart-container" style="height: 500px;"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.2.1/echarts.min.js"></script>


    <script>
        $(document).ready(function () {
            ajaxDeviceLine();
        });

        function ajaxcall() {
            var symbol;
            var brand = $('#beerbrand').val();
            var type = $("#type").val();
            var daysfilter = $("#daysfilter").val();
            var daysfilter2 = $("#daysfilter2").val();

            valueCall(type);
            loadData(brand, type, daysfilter, daysfilter2, symbol)
        }

        function valueCall(type) {
            if (type == 'temp') {
                symbol = 'Temperature (F)';
            }
            if (type == 'pre') {
                symbol = 'Pressure (PSI)';
            }
            if (type == 'tds') {
                symbol = 'TDS';
            }
        }

        $(document).ready(function () {

            $('#refreshBtn').on('click', function () {
                var symbol;
                var brand = $('#beerbrand').val();
                var type = $("#type").val();

                var daysfilter = $("#daysfilter").val();
                var daysfilter2 = $("#daysfilter2").val();

                valueCall(type);
                loadData(brand, type, daysfilter, daysfilter2, symbol)
            });

            // $('#beerbrand').on('change', function() {
            //     var symbol;
            //     var brand = $('#beerbrand').val();
            //     var type = $("#type").val();

            //     var daysfilter = $("#daysfilter").val();
            //     var daysfilter2 = $("#daysfilter2").val();

            //     valueCall(type);
            //     loadData(brand, type, daysfilter, daysfilter2, symbol)
            // });
            // $('#type').on('change', function() {
            //     var symbol;
            //     var brand = $('#beerbrand').val();
            //     var type = $("#type").val();
            //     var daysfilter = $("#daysfilter").val();
            //     var daysfilter2 = $("#daysfilter2").val();

            //     valueCall(type);
            //     loadData(brand, type, daysfilter, daysfilter2, symbol)

            // });
            // $('#daysfilter').on('change', function() {
            //     var symbol;
            //     var brand = $('#beerbrand').val();
            //     var type = $("#type").val();
            //     var daysfilter = $("#daysfilter").val();
            //     var daysfilter2 = $("#daysfilter2").val();

            //     valueCall(type);
            //     loadData(brand, type, daysfilter, daysfilter2, symbol)

            // });

        });
    </script>

    <script>
        var choices = new Choices('#beerbrand', {
            allowSearch: false,
            removeItemButton: true,
            maxItemCount: 100,
            searchResultLimit: 100,
            renderChoiceLimit: 100
        });

        function ajaxDeviceLine() {
            $('#beerbrand').html('');
            var LocationGet = $('#LocationGet').val();

            //$('#beerbrand').empty();


            $.ajax({

                url: "{{ route('location.get') }}",
                type: 'GET',
                data: {
                    id: "LocationDevices",
                    LocationID: LocationGet,
                },
                success: function (data) {


                    choices.clearStore();

                    $('#beerbrand').html('');


                    var values = [];

                    data.forEach(function (value, index) {
                        var choice = {
                            value: value.DevicesID,
                            label: value.Name
                        };

                        if (index === 0) {
                            choices.setValue(choice);
                        } else {
                            values.push(choice);
                        }
                    });

                    if (values.length > 0) {
                        choices.setChoices(values, 'value', 'label');

                    }


                },
                complete: function () {
                    ajaxcall();
                }
            });

        }

        function MultipleCallAjax() {
            var multipleCancelButton = new Choices('#LineData', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

        }


        $('#LocationGet').on('change', function () {
            ajaxDeviceLine();
        });


        function clearChartContainer() {
            var dom = document.getElementById('chart-container');
            var myChart = echarts.getInstanceByDom(dom);
            if (myChart) {
                myChart.dispose(); // Dispose the chart instance to remove the old chart
            }
            $('#chart-container').empty(); // Clear the chart container content
        }

        function loadData(brand, type, daysfilter, daysfilter2, devices, symbol) {
            $('#spinner-div').show(); //Load button clicked show spinner

            $.ajax({
                url: "{{ url('/api/load/line-sensor/data') }}" + "/" + brand + "/" + type + "/" + daysfilter +
                    "/" +
                    daysfilter2,
                type: 'GET',
                dataType: 'json',
                success: function (rawData) {


                    dom = document.getElementById('chart-container');

                    myChart = echarts.init(dom, null, {
                        renderer: 'canvas',
                        useDirtyRect: false
                    });

                    var app = {};

                    var option;

                    series11 = [];
                    lineData = [];
                    DateRTD = [];

                    $.each(rawData.data, function (index, value) {
                        if (value.Brand) {
                            brand = value.Brand;
                        } else {
                            brand = "";
                        }
                        lineName = brand + " (" + (index + 1) + ")";
                        if (rawData.type == 'temp') {
                            // Temp
                            dataTemp = value.Temp;
                            Temp = dataTemp.split(',');

                            datalist = {
                                name: lineName,
                                type: 'line',
                                data: Temp
                            }
                        } else if (rawData.type == 'pre') {
                            // Temp
                            dataTemp = value.Pres;
                            Temp = dataTemp.split(',');

                            datalist = {
                                name: lineName,
                                type: 'line',
                                data: Temp
                            }
                        } else if (rawData.type == 'tds') {
                            // Temp
                            dataTemp = value.TDS;
                            Temp = dataTemp.split(',');

                            datalist = {
                                name: lineName,
                                type: 'line',
                                data: Temp
                            }
                        }
                        series11.push(datalist);

                        // Date
                        dataDeviceLines = value.RDT;
                        var RDTdate = dataDeviceLines.split(',');

                        lineData.push(value.DeviceLinesID);
                        if (DateRTD.length !== rawData.dataTime.length) {
                            for (let i = 0; i < rawData.dataTime.length; i++) {
                                DateRTD.push(rawData.dataTime[i]);
                            }
                        }
                    });

                    option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross'
                            }
                        },

                        legend: {
                            top: 0,
                        },
                        grid: {
                            top: '25%',
                            left: '3%',
                            right: '4%',
                            bottom: '0%',
                            containLabel: true
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                magicType: {
                                    type: ['line', 'bar']
                                }
                            },

                            top: 50,
                        },
                        xAxis: [{
                            type: 'category',
                            axisTick: {
                                alignWithLabel: true
                            },
                            axisLabel: {
                                rotate: 20
                            },
                            data: DateRTD
                        }],
                        yAxis: [{
                            type: 'value',
                            position: 'left',
                            axisLabel: {
                                formatter: '{value}'
                            },
                        }],
                        series: series11,


                    };

                    if (option && typeof option === 'object') {
                        myChart.setOption(option, true);
                    }

                    window.addEventListener('resize', myChart.resize);
                },
                error: function (xhr, status, error) {
                    // Handle the error scenario
                    clearChartContainer();
                    $('#chart-container').text('Error loading data. Please try again later.');
                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });

        }
    </script>

@endsection
