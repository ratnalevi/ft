@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">

@section('title', 'Home')
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



    @foreach($allDevicesIds as $value)
        <input type="hidden" name="all_device_ids[]" id="all_device_ids" value="{{$value}}">
    @endforeach

    <div class="col-lg-12">
        <div class="card m-3">
            <div class="m-3">
                <h4>
                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                    <span class="ml-2">User Reporting</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-tachometer" aria-hidden="true"></i>
                    <span class="ml-3">Sensor Reporting (Temperature, Pressure and TDS) by Date/Time</span>

                </h5>
            </div>

            <div class="m-3">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-group">
                            {{--                            <select style="border-radius: 36px;" class="form-control beerbrand" id="beerbrand">--}}
                            {{--                                @foreach ($brands as $item)--}}
                            {{--                                    <option value="{{ $item->BeerBrandsID }}">{{ $item->Brand }}</option>--}}
                            {{--                                @endforeach--}}
                            {{--                                    <option value="all">All</option>--}}
                            {{--                            </select>--}}
                            <select id="beerbrand" class="form-control" placeholder="Select Brand" multiple
                                    style="height: 45px !important;">
                                @foreach ($brands as $item)
                                    <option value="{{ $item->BeerBrandsID }}">{{ $item->Brand }}</option>
                                @endforeach
                                <option selected value="all">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group">
                            <select class="form-control type" id="type" style="height: 45px !important;">
                                <option value="temp">Temperature (F)</option>
                                <option value="pre">Pressure (PSI)</option>
                                <option value="tds">TDS</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <select class="form-control" id="daysfilter" style="height: 45px !important;">
                            <option value="30">Last 30 days</option>
                            <option value="60">Last 60 days</option>
                            <option value="all">All</option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <select class="form-control devicesfilter" name="devices" style="height: 45px !important;"
                                id="devices">

                            @foreach ($devices as $items)
                                <option value="{{ $items->DevicesID }}">{{ $items->Name }}</option>
                            @endforeach
                            <option selected value="all">All</option>
                        </select>
                    </div>

                </div>
                <div class="row">
                    <!-- row -->
                    <div class="col-md-12">
                        <div class="card mg-b-20">
                            <div class="card-body">
                                <div class="main-content-label mg-b-5 pt-2pb-5 text-center">
                                    <span id="Tile"></span>
                                </div>
                                <div id="main" style="height: 500px;"></div>

                            </div>
                        </div>
                    </div><!-- col-6 -->
                    <!-- /row -->
                </div>

            </div>

        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.2.1/echarts.min.js"></script>
    {{--      <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.1.0/echarts-en.common.min.js"></script>--}}


    <script>
        $(document).ready(function () {

            var multipleCancelButton = new Choices('#beerbrand', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

            // var multipleCancelButton2 = new Choices('#devices', {
            //     removeItemButton: true,
            //     maxItemCount:100,
            //     searchResultLimit:100,
            //     renderChoiceLimit:100
            // });

            $('#beerbrand').on('change', function () {
                var symbol;
                var brand = $('#beerbrand option:selected')
                    .toArray().map(item => item.value).join();
                // var brand = $('#beerbrand').val();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var devices = $('#devices option:selected')
                    .toArray().map(item => item.value).join();
                if (type == 'temp') {
                    symbol = 'Temperature (F)';
                }
                if (type == 'pre') {
                    symbol = 'Pressure (PSI)';
                }
                if (type == 'tds') {
                    symbol = 'TDS';
                }

                if (brand === null || brand === undefined || brand === '') {

                    $("#devices").empty();
                    $("#devices").html(null);

                    $.ajax({
                        url: "{{ url('/get/devices/against/brand') }}/" + 0,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            $("#devices").empty();
                            $("#devices").html(null);
                            $('#devices').append('<option value="all">All</option>');
                            for (var i = 0; i < response.devices.length; i++) {
                                $('#devices').append('<option value="' + response.devices[i]['DevicesID'] + '">' + response.devices[i]['Name'] + '</option>');
                            }

                            loadData(brand, type, daysfilter, devices, symbol);
                        }
                    });
                } else {
                    $.ajax({
                        url: "{{ url('/get/devices/against/brand') }}/" + brand,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            $("#devices").empty();
                            $("#devices").html(null);
                            $('#devices').append('<option value="all">All</option>');
                            for (var i = 0; i < response.devices.length; i++) {
                                $('#devices').append('<option value="' + response.devices[i]['DevicesID'] + '">' + response.devices[i]['Name'] + '</option>');
                            }

                            loadData(brand, type, daysfilter, devices, symbol);
                        }
                    });
                }
            });

            $('#type').on('change', function () {
                let symbol;
                const brand = $('#beerbrand option:selected')
                    .toArray().map(item => item.value).join();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var devices = $('#devices option:selected')
                    .toArray().map(item => item.value).join();

                if (type === 'temp') {
                    symbol = 'Temperature (F)';
                }
                if (type === 'pre') {
                    symbol = 'Pressure (PSI)';
                }
                if (type === 'tds') {
                    symbol = 'TDS';
                }

                loadData(brand, type, daysfilter, devices, symbol);
            });

            $('#daysfilter').on('change', function () {
                var symbol;
                var brand = $('#beerbrand option:selected')
                    .toArray().map(item => item.value).join();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var devices = $('#devices option:selected')
                    .toArray().map(item => item.value).join();
                if (type == 'temp') {
                    symbol = 'Temperature (F)';
                }
                if (type == 'pre') {
                    symbol = 'Pressure (PSI)';
                }
                if (type == 'tds') {
                    symbol = 'TDS';
                }

                loadData(brand, type, daysfilter, devices, symbol);
            });

            $('#devices').on('change', function () {
                var symbol;
                var brand = $('#beerbrand option:selected')
                    .toArray().map(item => item.value).join();
                var type = $("#type").val();
                var daysfilter = $("#daysfilter").val();
                var devices = $('#devices option:selected')
                    .toArray().map(item => item.value).join();

                if (type == 'temp') {
                    symbol = 'Temperature (F)';
                }
                if (type == 'pre') {
                    symbol = 'Pressure (PSI)';
                }
                if (type == 'tds') {
                    symbol = 'TDS';
                }

                loadData(brand, type, daysfilter, devices, symbol);
            });

            var symbol = 'Temperature';
            var brand = $('#beerbrand option:selected')
                .toArray().map(item => item.value).join();
            // var brand = $('#beerbrand').val();
            var type = $("#type").val();
            var daysfilter = $("#daysfilter").val();
            var devices = $('#devices option:selected')
                .toArray().map(item => item.value).join();

            loadData(brand, type, daysfilter, devices, symbol);
        });

        function loadData(brand, type, daysfilter, devices, symbol) {

            var chartDom = document.getElementById('main');
            var myChart = echarts.init(chartDom);
            var option;

            if (brand === '' || brand == null) {
                $.ajax({
                    // url: 'http://127.0.0.1:8000/api/load/line/data/'+brand+'/'+type+'/'+daysfilter+'/'+devices,
                    url: "{{ url('/api/load/line/data') }}/0" + "/" + type + "/" + daysfilter + '/' + devices,
                    type: 'GET',
                    dataType: 'json',
                    success: function (_rawData) {

                        var ids = [];

                        var finalArray = [];
                        var responseString = "" + _rawData + "";
                        var resposneArray = responseString.split(',');

                        for (var i = 0; i < resposneArray.length; i++) {
                            if (resposneArray[i].indexOf('_&%') > -1) {
                                if (jQuery.inArray(resposneArray[i], ids) != -1) {

                                } else {
                                    ids.push(resposneArray[i]);
                                }

                            }
                        }

                        for (var j = 0; j < ids.length; j++) {
                            finalArray.push(ids[j].match(/\d+/));
                        }


                        // var brand = $('#beerbrand option:selected')
                        //      .toArray().map(item => item.value).join();
                        //
                        //  if(brand=='1,2' || brand=='2,1' || brand=='all'){
                        //      ids.push(1);
                        //      ids.push(2);
                        //  }else{
                        //      if(brand==1){
                        //          ids.push(1);
                        //      }if(brand==2){
                        //          ids.push(2);
                        //      }
                        //  }


                        var datasetWithFilters = [];
                        var seriesList = [];

                        echarts.util.each(finalArray, function (deviceIds) {
                            var datasetId = 'dataset_' + deviceIds;
                            datasetWithFilters.push({
                                id: datasetId,
                                fromDatasetId: 'dataset_raw',
                                transform: {
                                    type: 'filter',
                                    config: {
                                        and: [{dimension: 'ID', '=': deviceIds}]
                                    }
                                }
                            });
                            seriesList.push({
                                type: 'line',
                                datasetId: datasetId,
                                showSymbol: true,
                                name: deviceIds,
                                endLabel: {
                                    show: true,
                                    formatter: function (params) {
                                        return 'Brand ' + params.value[5] + ': Line ' + params.value[3];
                                    }
                                },
                                labelLayout: {
                                    moveOverlap: 'shiftY'
                                },
                                emphasis: {
                                    focus: 'series'
                                },
                                encode: {
                                    x: 'DateTimeReport',
                                    y: 'Income',
                                    label: ['Country', 'Income'],
                                    itemName: 'DateTimeReport',
                                    tooltip: ['Income']
                                }
                            });
                        });
                        option = {
                            animationDuration: 10000,
                            dataset: [
                                {
                                    id: 'dataset_raw',
                                    source: _rawData
                                },
                                ...datasetWithFilters
                            ],
                            title: {
                                text: 'Sensor Reporting'
                            },
                            tooltip: {
                                order: 'valueDesc',
                                trigger: 'axis'
                            },
                            ///////////
                            legend: {},
                            toolbox: {
                                show: true,
                                feature: {
                                    // dataZoom: {
                                    //     yAxisIndex: 'none'
                                    // },
                                    dataView: {readOnly: false},
                                    // magicType: { type: ['line', 'bar'] },
                                    restore: {},
                                    saveAsImage: {}
                                }
                            },
                            //////////
                            xAxis: {
                                type: 'time',
                                boundaryGap: false
                            },
                            yAxis: {
                                name: symbol
                            },
                            grid: {
                                right: 140
                            },
                            series: seriesList
                        };
                        myChart.setOption(option, true);

                        option && myChart.setOption(option);

                    }
                });
            } else {
                $.ajax({
                    // url: 'http://127.0.0.1:8000/api/load/line/data/'+brand+'/'+type+'/'+daysfilter+'/'+devices,
                    url: "{{ url('/api/load/line/data') }}/" + brand + "/" + type + "/" + daysfilter + '/' + devices,
                    type: 'GET',
                    dataType: 'json',
                    success: function (_rawData) {

                        var ids = [];

                        var finalArray = [];
                        var responseString = "" + _rawData + "";
                        var resposneArray = responseString.split(',');

                        for (var i = 0; i < resposneArray.length; i++) {
                            if (resposneArray[i].indexOf('_&%') > -1) {
                                if (jQuery.inArray(resposneArray[i], ids) != -1) {

                                } else {
                                    ids.push(resposneArray[i]);
                                }

                            }
                        }

                        for (var j = 0; j < ids.length; j++) {
                            finalArray.push(ids[j].match(/\d+/));
                        }


                        // var brand = $('#beerbrand option:selected')
                        //      .toArray().map(item => item.value).join();
                        //
                        //  if(brand=='1,2' || brand=='2,1' || brand=='all'){
                        //      ids.push(1);
                        //      ids.push(2);
                        //  }else{
                        //      if(brand==1){
                        //          ids.push(1);
                        //      }if(brand==2){
                        //          ids.push(2);
                        //      }
                        //  }


                        var datasetWithFilters = [];
                        var seriesList = [];

                        echarts.util.each(finalArray, function (deviceIds) {
                            var datasetId = 'dataset_' + deviceIds;
                            datasetWithFilters.push({
                                id: datasetId,
                                fromDatasetId: 'dataset_raw',
                                transform: {
                                    type: 'filter',
                                    config: {
                                        and: [{dimension: 'ID', '=': deviceIds}]
                                    }
                                }
                            });
                            seriesList.push({
                                type: 'line',
                                datasetId: datasetId,
                                showSymbol: true,
                                name: deviceIds,
                                endLabel: {
                                    show: true,
                                    formatter: function (params) {
                                        return 'Brand ' + params.value[5] + ': Line ' + params.value[3];
                                    }
                                },
                                labelLayout: {
                                    moveOverlap: 'shiftY'
                                },
                                emphasis: {
                                    focus: 'series'
                                },
                                encode: {
                                    x: 'DateTimeReport',
                                    y: 'Income',
                                    label: ['Country', 'Income'],
                                    itemName: 'DateTimeReport',
                                    tooltip: ['Income']
                                }
                            });
                        });
                        option = {
                            animationDuration: 10000,
                            dataset: [
                                {
                                    id: 'dataset_raw',
                                    source: _rawData
                                },
                                ...datasetWithFilters
                            ],
                            title: {
                                text: 'Sensor Reporting'
                            },
                            tooltip: {
                                order: 'valueDesc',
                                trigger: 'axis'
                            },
                            ///////////
                            legend: {},
                            toolbox: {
                                show: true,
                                feature: {
                                    // dataZoom: {
                                    //     yAxisIndex: 'none'
                                    // },
                                    dataView: {readOnly: false},
                                    // magicType: { type: ['line', 'bar'] },
                                    restore: {},
                                    saveAsImage: {}
                                }
                            },
                            //////////
                            xAxis: {
                                type: 'time',
                                boundaryGap: false
                            },
                            yAxis: {
                                name: symbol
                            },
                            grid: {
                                right: 140
                            },
                            series: seriesList
                        };
                        myChart.setOption(option, true);

                        option && myChart.setOption(option);

                    }
                });
            }


        }
    </script>

@endsection
