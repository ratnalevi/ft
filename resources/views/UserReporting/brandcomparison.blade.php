@extends('layouts.master')


<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

@section('title', 'Brand Comparison')
@section('content')

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

        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        #chart {
            margin: 0 auto;
        }

        #chart-container {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        input.choices__input.choices__input--cloned {
            width: 7px;
        }

        .choices[data-type*=select-multiple] .choices__inner,
        .choices[data-type*=text] .choices__inner {
            cursor: text;
            border-radius: 36px;
        }
    </style>

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
                                <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}</option>
                            @else
                                <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                            @endif
                        @endforeach
                        {{-- @foreach ($locationsUsers as $items)
                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                        @endforeach --}}
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="row m-3">
                <div class="col-lg-12">
                    <h5>
                        <i class="fa fa-window-restore" aria-hidden="true"></i>
                        <span class="ml-3">Brand Comparison</span>
                    </h5>
                </div>
                <div class="col-lg-12">
                    <a id="refreshBtn" href="#">
                        <i class="fa fa-refresh" aria-hidden="true" style="font-size: 100%;float: right;"> Refresh
                        </i>
                    </a>
                </div>

                <div style="display: flex" class="col-lg-12">

                    <div style="padding: 0px;" class="col-lg-3 mt-3">
                        <select id="mySelect" class="form-control devices" multiple
                                style="height: 49px !important; border-radius: 36px;" name="devices">

                        </select>

                    </div>
                    <div class="col-lg-3  mt-3">
                        <select class="form-control types" style="height: 49px !important;border-radius: 36px"
                                name="types" id="types">
                            <option selected value="ounces">Ounces Poured</option>
                            <option value="revenue">Revenue</option>
                        </select>
                    </div>
                    @php

                        $currentDateTime = new DateTime();
                        $currentDateTime->modify('-15 days');
                        $currentDateFifteenAgo = $currentDateTime->format('Y-m-d\T06:00');

                        $currentDateTime = new DateTime();
                        $currentDateTime->modify('-1 day');
                        $currentDateOneAgo = $currentDateTime->format('Y-m-d\T06:00');

                        $timestamp = time();
                        $currentDate = date('Y-m-d\TH:i', $timestamp);

                        $minDateTime = new DateTime();
                        $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                        $currentDateTime = new DateTime();
                        $maxDateString = $currentDateTime->format('Y-m-d\TH:i');
                    @endphp
                    <div class="col-lg-3 mt-3">
                        <input style="height: 49px !important; margin: 0px 25px 0px 0px;border-radius: 36px;"
                               id="fromdate"
                               type="datetime-local" class="form-control" value="{{ $currentDateFifteenAgo }}"
                               min="{{ $minDateString }}" max="{{ $maxDateString }}">
                    </div>
                    <div style="padding: 0px;" class="col-lg-3 mt-3">
                        <input type="datetime-local" style="height: 49px !important; border-radius: 36px;" id="todate"
                               class="form-control" value="{{ $currentDateOneAgo }}" min="{{ $minDateString }}"
                               max="{{ $maxDateString }}">
                    </div>

                </div>

                <div id="spinner-div" class="pt-5">
                    <div class="mt-5">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 1%;">
                    <div class="card mg-b-20">
                        <div class="card-body">
                            {{-- <div
                        class="main-content-label mg-b-5 pt-2pb-5 text-center location_by_pourscor_percentage">
                        Brand Comparison by PourScore Percentage
                    </div> --}}
                            <div class="main-content-label mg-b-5 pt-2pb-5 text-center location_by_ounces_poured">
                                <span id="ReportName"></span>
                                Brand Comparison by Ounces Poured
                            </div>
                            {{-- <div class="main-content-label mg-b-5 pt-2pb-5 text-center location_by_revenue"
                        style="display: none;">
                        Brand Comparison by Revenue
                    </div> --}}
                            <div id="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('#LocationGet').on('change', function () {
                ajaxChangeLocation();
            });
            var choices = new Choices('#mySelect', {
                allowSearch: false,
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

            function ajaxChangeLocation() {
                $('#mySelect').html("");
                var location = $('#LocationGet').val();
                $.ajax({
                    type: 'GET',
                    url: '/get/brand/' + location,
                    success: function (data) {
                        choices.clearStore();
                        $('#mySelect').html("");

                        var values = [];

                        data.forEach(function (value, index) {
                            var choice = {
                                value: value.BeerBrandsID,
                                label: value.Brand
                            };

                            values.push(choice);
                        });

                        if (values.length > 0) {
                            var existingChoices = choices.getValue(true); // Get existing choices
                            if (existingChoices.length === 0) {
                                choices.setChoices(values, 'value', 'label');
                                choices.setValue('All');
                            } else {
                                choices.clearChoices(); // Clear existing choices
                            }
                        } else if (values.length <= 0) {
                            choices.setChoices(values, 'value', 'label');
                            choices.setValue('All');
                        } else {
                            choices.clearChoices(); // Clear existing choices
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle the error scenario
                        clearChartContainer();
                        $('#chart-container').text('Error loading data. Please try again later.');
                    },
                    complete: function () {
                        var deviceName = $('#mySelect option:selected').toArray().map(item => item
                            .value)
                            .join();
                        console.log("-" + deviceName);
                        var types = $('#types').val();
                        var fromDate = $('#fromdate').val();
                        var ToDate = $('#todate').val();
                        var location = $('#LocationGet').val();
                        ajaxCall(location, deviceName, types, fromDate, ToDate);
                    }
                });
            }

            function clearChartContainer() {
                var dom = document.getElementById('chart-container');
                var myChart = echarts.getInstanceByDom(dom);
                if (myChart) {
                    myChart.dispose(); // Dispose the chart instance to remove the old chart
                }
                $('#chart-container').empty(); // Clear the chart container content
            }

            ajaxChangeLocation();

            $('#refreshBtn').on('click', function () {
                var deviceName = $('#mySelect option:selected').toArray().map(item => item.value).join();
                if (!deviceName) {
                    toastr.options = {
                        "closeButton": true,
                        "progressBar": true
                    }
                }
                var types = $('#types').val();
                var fromDate = $('#fromdate').val();
                var ToDate = $('#todate').val();

                var location = $('#LocationGet').val();
                ajaxCall(location, deviceName, types, fromDate, ToDate);
            });

            // $('#mySelect').on('change', function() {
            //     var deviceName = $('#mySelect option:selected').toArray().map(item => item.value).join();
            //     var types = $('#types').val();
            //     var fromDate = $('#fromdate').val();
            //     var ToDate = $('#todate').val();
            //     ajaxCall(deviceName, types, fromDate, ToDate);
            // });
            // $('#fromdate').on('change', function() {
            //     var deviceName = $('#mySelect option:selected').toArray().map(item => item.value).join();
            //     var types = $('#types').val();
            //     var fromDate = $('#fromdate').val();
            //     var ToDate = $('#todate').val();
            //     ajaxCall(deviceName, types, fromDate, ToDate);
            // });
            // $('#todate').on('change', function() {
            //     var deviceName = $('#mySelect option:selected').toArray().map(item => item.value).join();
            //     var types = $('#types').val();
            //     var fromDate = $('#fromdate').val();
            //     var ToDate = $('#todate').val();
            //     ajaxCall(deviceName, types, fromDate, ToDate);
            // });

            function ajaxCall(location, deviceName, types, fromDate, ToDate) {
                $('#spinner-div').show(); //Load button clicked show spinner
                $.ajax({
                    type: 'GET',
                    url: '/brand-comparison/load/data/' + location + '/' + deviceName + '/' + types + '/' +
                        fromDate + '/' + ToDate,
                    success: function (dataList) {
                        seriesBrand = [];
                        seriesOunces = [];
                        $.each(dataList, function (index, value) {
                            seriesBrand.push(value.Brand);
                            seriesOunces.push(value.Ounces);
                        });
                        var dom = document.getElementById('chart-container');
                        var myChart = echarts.init(dom, null, {
                            renderer: 'canvas',
                            useDirtyRect: false
                        });
                        var app = {};
                        var option;
                        option = {
                            toolbox: {
                                show: true,
                                feature: {
                                    magicType: {
                                        type: ['line', 'bar']
                                    },
                                    saveAsImage: {}
                                }
                            },
                            xAxis: {
                                type: 'category',
                                axisLabel: {
                                    interval: 0,
                                    rotate: 30
                                },
                                data: seriesBrand,
                            },
                            yAxis: {
                                type: 'value'
                            },
                            series: [{
                                data: seriesOunces,
                                type: 'bar',
                                showBackground: true,
                                backgroundStyle: {
                                    color: 'rgba(180, 180, 180, 0.2)'
                                }
                            }]
                        };
                        option.tooltip = {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'shadow'
                            },
                            formatter: function (params) {
                                var dataIndex = params[0].dataIndex;
                                var value = seriesOunces[dataIndex];
                                console.log(dataIndex);
                                return ' ' + value;
                            }
                        };
                        if (option && typeof option === 'object') {
                            myChart.setOption(option);
                        }
                        window.addEventListener('resize', myChart.resize);
                    },
                    complete: function () {
                        $('#spinner-div').hide(); //Request is complete so hide spinner
                    }
                });
            }
        });
    </script>

@endsection
