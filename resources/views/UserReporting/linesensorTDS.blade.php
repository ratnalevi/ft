@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">

@section('title', 'Line TDS Report')
@section('content')
    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        #chart-container {
            margin: 0 auto;
        }

        .form-control {
            font-size: 12px;
        }
    </style>

    <input type="hidden" name="last_device" id="last_device" value="{{ $lastDevice->DevicesID }}">



    @foreach ($allDevicesIds as $value)
        <input type="hidden" name="all_device_ids[]" id="all_device_ids" value="{{ $value }}">
    @endforeach

    <div class="col-lg-12">
        <div class="card">
            <div class="row m-3">
                <div class="col-lg-8 mt-4">
                    <h4>
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="ml-3">Select a location</span>

                    </h4>
                </div>

                <div class="col-lg-4 mt-4">
                    <select class="form-control locationLOca" style="border-radius: 36px;" name="location33"
                            id="LocationGet">
                        @foreach ($locations as $items)
                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <h5>
                    <i class="fa fa-tachometer" aria-hidden="true"></i>
                    <span class="ml-3">Sensor Reporting (Temperature, Pressure and TDS) by Date/Time</span>

                </h5>
            </div>

            <div class="m-3">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            <select id="beerbrand" class="form-control" placeholder="Select Brand"
                                    style="border-radius: 21px;">

                            </select>
                        </div>
                    </div>
                    {{-- <div class="col-lg-3">
                        <div class="form-group">
                            <select class="form-control type" id="type" style="border-radius: 21px;">
                                <option value="temp">Temperature (F)</option>
                                <option value="pre">Pressure (PSI)</option>
                                <option value="tds">TDS</option>
                            </select>
                        </div>
                    </div> --}}
                    @php
                        $timestamp = time();
                        $date90DaysAgo = strtotime('-7 days', $timestamp);
                        $dateString = date('Y-m-d\T00:00', $date90DaysAgo);
                        $currentDate = date('Y-m-d\TH:i');

                        $minDateTime = new DateTime();
                        $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                        $currentDateTime = new DateTime();
                        $maxDateString = $currentDateTime->format('Y-m-d\TH:i');
                    @endphp
                    <div class="col-lg-3">
                        <input style="margin: 0px 25px 0px 0px;    border-radius: 21px;"
                               id="daysfilter" type="datetime-local" class="form-control" value="{{ $dateString }}"
                               min="{{ $minDateString }}" max="{{ $maxDateString }}">
                    </div>

                    <div class="col-lg-3">
                        <input style="border-radius: 21px;" type="datetime-local" id="daysfilter2"
                               class="form-control devicesfilter" value="{{ $currentDate }}" min="{{ $minDateString }}"
                               max="{{ $maxDateString }}">
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mg-b-20">
                            <div class="card-body">
                                <div class="main-content-label mg-b-5 pt-2pb-5 text-center">
                                    <span class="mb-3">TDS</span>
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
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.1.0/echarts-en.common.min.js"></script> --}}


    <script>
        $(document).ready(function () {
            ajaxcall();
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
            $('#beerbrand').on('change', function () {
                var symbol;
                var brand = $('#beerbrand').val();
                var type = $("#type").val();

                var daysfilter = $("#daysfilter").val();
                var daysfilter2 = $("#daysfilter2").val();

                valueCall(type);
                loadData(brand, type, daysfilter, daysfilter2, symbol)
            });
            $('#type').on('change', function () {
                var symbol;
                var brand = $('#beerbrand').val();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var daysfilter2 = $("#daysfilter2").val();

                valueCall(type);
                loadData(brand, type, daysfilter, daysfilter2, symbol)

            });
            $('#daysfilter').on('change', function () {
                var symbol;
                var brand = $('#beerbrand').val();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var daysfilter2 = $("#daysfilter2").val();

                valueCall(type);
                loadData(brand, type, daysfilter, daysfilter2, symbol)

            });


        });


        function loadData(brand, type, daysfilter, daysfilter2, devices, symbol) {
            $.ajax({
                url: "{{ url('/api/load/line-sensor/data') }}" + "/" + brand + "/" + type + "/" + daysfilter + "/" +
                    daysfilter2,
                type: 'GET',
                dataType: 'json',
                success: function (rawData) {

                    var dom = document.getElementById('chart-container');
                    var myChart = echarts.init(dom, null, {
                        renderer: 'canvas',
                        useDirtyRect: false
                    });
                    var app = {};

                    var option;

                    var series11 = [];
                    var lineData = [];
                    var DateRTD = [];

                    $.each(rawData, function (index, value) {

                        // Temp
                        dataTemp = value.TDS;
                        var Temp = dataTemp.split(',');

                        var lineName = 'Line ' + (index + 1);

                        // Date
                        dataDeviceLines = value.RDT;
                        var RDTdate = dataDeviceLines.split(',');


                        lineData.push('Line ' + value.DeviceLinesID);
                        DateRTD.push(RDTdate[0]);
                        console.log(DateRTD[0]);

                        datalist = {
                            name: lineName,
                            type: 'line',
                            data: Temp
                        }
                        series11.push(datalist);
                    });

                    option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross'
                            }
                        },
                        legend: {},
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
                            // name: 'Temperature',
                            position: 'left',
                            axisLabel: {
                                formatter: '{value}'
                            }
                        }],
                        series: series11,
                    };

                    if (option && typeof option === 'object') {
                        myChart.setOption(option);
                    }

                    window.addEventListener('resize', myChart.resize);

                }
            });

        }
    </script>


    <script>
        function ajaxDeviceLine() {
            $('#beerbrand').html('');
            var LocationGet = $('#LocationGet').val();
            $.ajax({
                url: "{{ route('location.get') }}",
                type: 'GET',
                data: {
                    id: "LocationDevices",
                    LocationID: LocationGet,
                },
                success: function (data) {
                    $('#beerbrand').html('');

                    deviceLineOption = '';
                    data.forEach(element => {
                        deviceLineOption += '<option value=' + element.DevicesID + ' >' +
                            element
                                .Name + '</option>'
                    });
                    $('#beerbrand').append(deviceLineOption);
                }
            });
        }

        $('#LocationGet').on('change', function () {
            ajaxDeviceLine();
        });
    </script>

@endsection
