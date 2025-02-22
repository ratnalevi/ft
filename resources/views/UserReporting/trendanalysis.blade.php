@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

@section('title', 'Trend Analysis')
@section('content')

    <style>
        .mt-5 {
            padding-top: 96px;
            padding-left: 96px;
        }

        #spinner-div {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
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

        /*#chart-container2 {*/
        /*    position: relative;*/
        /*    height: 100vh;*/
        /*    overflow: hidden;*/
        /*}*/
        /*#revenue-chart-container {*/
        /*    position: relative;*/
        /*    height: 100vh;*/
        /*    overflow: hidden;*/
        /*}*/

        .choices[data-type*=select-multiple] .choices__inner,
        .choices[data-type*=text] .choices__inner {
            cursor: text;
            border-radius: 36px;
        }

        .choices {
            position: relative;
            margin-bottom: 16px;
            font-size: 16px;
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
                        {{-- @foreach ($locationsUsers as $items)
                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                        @endforeach --}}
                        @foreach ($locationsUsers as $items)
                            @if ($items->LocationID == $name->LocationID)
                                <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}</option>
                            @else
                                <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div id="spinner-div" class="pt-5">
            <div class="mt-5">
                <div class="spinner-border text-primary" role="status">
                </div>
            </div>
        </div>
        <div class="card">
            <div class="row">
                <div class="m-3 col-md-8 col-lg-8">
                    <h5>
                        <i class="fa fa-window-restore" aria-hidden="true"></i>
                        <span class="ml-3">Trend Analysis - By Single Brand or All Brands</span>
                    </h5>
                </div>
                <div class="m-3 col-md-3 col-lg-3">
                        <a id="refreshBtn" href="#">
                            <i class="fa fa-refresh" aria-hidden="true"
                               style="font-size: 100%;float: right; padding-right: 21px;"> Refresh
                            </i>
                        </a>
                </div>
            </div>
            <div class="m-1">
                <div class="row">
                    <div class="col-lg-12 d-flex">
                        <div class="col-lg-6">
                            <label for="">Select Brand</label>
                            <select class="form-control brand" style="height: 49px !important; border-radius: 36px;"
                                    id="brand" style="border-radius: 36px;" multiple>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label style="color: white" for="">Select Brand</label>
                            <select class="form-control types" style="height: 49px !important; border-radius: 36px;"
                                    name="types" id="types">
                                <option value="pressure">Pressure</option>
                                <option value="ounces">Ounces Poured</option>
                                <option value="pourscore">PourScore Percentage</option>
                                <option value="revenue">Revenue</option>
                                <option value="temperature">Temperature</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-lg-12 d-flex" style="padding-top: 4px;">
                        <div class="col-lg-12">
                            <div class="d-flex">
                                <label for="" class="mt-3" style="width: 708px;">Enter Period Length:</label>
                                <input type="text" class="form-control" value="1" id="wName" name="wName"
                                       style=" width: 76px;text-align: center;height: 49px !important; margin-right: 6px; border-radius: 36px;">

                                <select name="WeeName" id="WeeName" class="form-control"
                                        style="width: 109px;height: 49px !important; border-radius: 36px;">
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="day">Day</option>
                                </select>

                                @php
                                    $minDateTime = new DateTime();
                                    $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                                    $currentDateTime = new DateTime();
                                    $maxDateString = $currentDateTime->format('Y-m-d\TH:i');
                                @endphp

                                <label for="" style="width: 600px; text-align: center;" class="mt-3">Period 1
                                    Start:</label>
                                <input type="datetime-local" style="height: 49px !important; border-radius: 36px;"
                                       class="form-control" name="period1" value="{{ $span1Start }}" id="period1"
                                       min="{{ $minDateString }}" max="{{ $maxDateString }}">

                                <label class="mt-3" style="margin-right: 18px; width: 600px;text-align: center;"
                                       for="">Period 2 Start:</label>
                                <input type="datetime-local"
                                       style="height: 49px !important; border-radius: 36px;
                                    margin-left: -20px;"
                                       class="form-control" name="period2" value="{{ $span2Start }}" id="period2"
                                       min="{{ $minDateString }}" max="{{ $maxDateString }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row -->
            <div class="col-md-12">
                <div class="card mg-b-20">
                    <div class="card-body">
                        <div id="chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>
    <script type="text/javascript">
        var fromDate = 'period1';
        var toDate = 'period2';

        $('#LocationGet').on('change', function () {
            choices.clearStore();
            $('#brand').html("");
            var location = $('#LocationGet').val();
            $.ajax({
                type: 'GET',
                url: '/get/brand/' + location,
                success: function (data) {
                    choices.clearStore();
                    $('#brand').html("");

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
            });
        });
        var choices = new Choices('#brand', {
            allowSearch: false,
            removeItemButton: true,
            maxItemCount: 100,
            searchResultLimit: 100,
            renderChoiceLimit: 100
        });

        function ajaxChangeLocation() {
            $('#brand').html("");
            var location = $('#LocationGet').val();
            $.ajax({
                type: 'GET',
                url: '/get/brand/' + location,
                success: function (data) {
                    choices.clearStore();
                    $('#brand').html("");

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
                complete: function () {
                    var brand = $('#brand option:selected').toArray().map(item => item
                        .value)
                        .join();

                    //span 1
                    var fromdate_span1 = $("#fromdate_span1").val();
                    var todate_span1 = $("#todate_span1").val();


                    // span 2
                    var fromdate_span2 = $("#fromdate_span2").val();
                    var todate_span2 = $("#todate_span2").val();

                    var type = $("#types").val();

                    loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

                }
            });
        }

        ajaxChangeLocation();

        $(document).ready(function () {
            var location = $('.locationLOca').val();

            // $.ajax({
            //     type: 'GET',
            //     url: '/get/devices/' + location,
            //     success: function(data) {
            //         $('#brand').html('');

            //         option = '';
            //         data.forEach(element => {
            //             option += '<option value=' + element.DevicesID + '>' + element.Name +
            //                 '</option>';
            //         });

            //         option += '<option selected value="all">All</option>';
            //         $('#brand').append(option);
            //     },
            // });

            $('#refreshBtn').on('click', function () {

                var brand = $(this).val();

                var brand = $("#brand").val();

                //span 1
                var fromdate_span1 = $("#fromdate_span1").val();
                var todate_span1 = $("#todate_span1").val();


                // span 2
                var fromdate_span2 = $("#fromdate_span2").val();
                var todate_span2 = $("#todate_span2").val();

                var type = $("#types").val();

                loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);
            });
        });

        // $('#brand').on('change', function() {

        //     var brand = $(this).val();

        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $("#fromdate_span1").val();
        //     var todate_span1 = $("#todate_span1").val();

        //     // span 2
        //     var fromdate_span2 = $("#fromdate_span2").val();
        //     var todate_span2 = $("#todate_span2").val();

        //     var type = $("#types").val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);


        // });

        // $('#types').on('change', function() {


        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $("#fromdate_span1").val();
        //     var todate_span1 = $("#todate_span1").val();

        //     // span 2
        //     var fromdate_span2 = $("#fromdate_span2").val();
        //     var todate_span2 = $("#todate_span2").val();

        //     var type = $(this).val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);


        // });

        // $('#fromdate_span1').on('change', function() {

        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $(this).val();
        //     var todate_span1 = $("#todate_span1").val();

        //     // span 2
        //     var fromdate_span2 = $("#fromdate_span2").val();
        //     var todate_span2 = $("#todate_span2").val();

        //     var type = $("#types").val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

        // });

        // $('#fromdate_span2').on('change', function() {

        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $("#fromdate_span1").val();
        //     var todate_span1 = $("#todate_span1").val();

        //     // span 2
        //     var fromdate_span2 = $(this).val();
        //     var todate_span2 = $("#todate_span2").val();

        //     var type = $("#types").val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

        // });

        // $('#todate_span1').on('change', function() {

        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $("#fromdate_span1").val();
        //     var todate_span1 = $(this).val();

        //     // span 2
        //     var fromdate_span2 = $("#fromdate_span2").val();
        //     var todate_span2 = $("#todate_span2").val();

        //     var type = $("#types").val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

        // });

        // $('#todate_span2').on('change', function() {

        //     var brand = $("#brand").val();

        //     //span 1
        //     var fromdate_span1 = $("#fromdate_span1").val();
        //     var todate_span1 = $("#todate_span1").val();

        //     // span 2
        //     var fromdate_span2 = $("#fromdate_span2").val();
        //     var todate_span2 = $(this).val();

        //     var type = $("#types").val();

        //     loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

        // });

        $('#ddlFruits').on('change', function () {
            var beerbrand = $('#ddlFruits').val();
            $.ajax({
                url: "{{ url('change_location') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    id: "beerbrandName",
                    beerbrand: beerbrand,
                },
                success: function (dataList) {
                    console.log(dataList);
                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });
        });

        $(document).ready(function () {

            var brand = $("#brand").val();

            //span 1
            var fromdate_span1 = $("#fromdate_span1").val();
            var todate_span1 = $("#todate_span1").val();

            // span 2
            var fromdate_span2 = $("#fromdate_span2").val();
            var todate_span2 = $("#todate_span2").val();

            var type = $("#types").val();

            // loadDataOnload(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type);

        });

        function loadData(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type) {
            $('#spinner-div').show();


            /*
                        from = new Date(fromdate_span1);
                        to = new Date(todate_span1);

                        diffMilli = (to - from);
                        console.log(diffMilli + "--");

                        // - 1
                        var date1 = new Date(fromdate_span1);
                        date1.setDate(date1.getDate());

                        var year = date1.getFullYear();
                        var month = ('0' + (date1.getMonth() + 1)).slice(-2);
                        var day = ('0' + date1.getDate()).slice(-2);
                        var hours = ('0' + date1.getHours()).slice(-2);
                        var minutes = ('0' + date1.getMinutes()).slice(-2);
                        var seconds = ('0' + date1.getSeconds()).slice(-2);

                        var dateString = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;



                        //


                        var date = new Date(fromdate_span1);

                        var timestamp = date.getTime(); // Get the timestamp of the date in milliseconds
                        var newTimestamp = timestamp - diffMilli; // Subtract the milliseconds

                        var newDate = new Date(newTimestamp); // Create a new Date object with the updated timestamp

                        var year = newDate.getFullYear();
                        var month = ('0' + (newDate.getMonth() + 1)).slice(-2);
                        var day = ('0' + newDate.getDate()).slice(-2);
                        var hours = ('0' + newDate.getHours()).slice(-2);
                        var minutes = ('0' + newDate.getMinutes()).slice(-2);
                        var seconds = ('0' + newDate.getSeconds()).slice(-2);

                        var dateString2 = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;




                        $('#todate_span2').val(dateString);
                        $('#fromdate_span2').val(dateString2);
                        console.log(fromdate_span1 + "-spn1-" + todate_span1);
                        console.log(fromdate_span2 + "-spn1-" + todate_span2);


                        fromdate_span2 = dateString2;
                        todate_span2 = dateString;

            */

            var Diff = $('#wName').val();
            var typeDiff = $('#WeeName').val();
            var diffPicked = 0;
            var fromdate_span1 = $('#period1').val();
            var fromdate_span2 = $('#period2').val();
            var location = $('#LocationGet').val();
            console.log(fromdate_span1 + "---val---" + fromdate_span2);

            var fromdate_span1Date = new Date(fromdate_span1);
            var fromdate_span2Date = new Date(fromdate_span2);

            if (typeDiff == "month") {
                diffPicked = 2629800000; //milliseconds in one month


            } else if (typeDiff == "week") {
                diffPicked = 604800000; //milliseconds in one week

            } else if (typeDiff == "day") {
                diffPicked = 86400000;
            }

            var newDateTime = new Date(fromdate_span1Date.getTime() + (Diff * diffPicked));
            var hours = newDateTime.getUTCHours().toString().padStart(2, '0');
            var minutes = newDateTime.getUTCMinutes().toString().padStart(2, '0');
            var todate_span1 = `${newDateTime.toISOString().split('T')[0]}T${hours}:${minutes}`;


            newDateTime = new Date(fromdate_span2Date.getTime() + (Diff * diffPicked));
            hours = newDateTime.getUTCHours().toString().padStart(2, '0');
            minutes = newDateTime.getUTCMinutes().toString().padStart(2, '0');
            var todate_span2 = `${newDateTime.toISOString().split('T')[0]}T${hours}:${minutes}`;


            console.log(todate_span1 + "---aftercalc1---" + fromdate_span1);


            console.log(todate_span2 + "---aftercalc2---" + fromdate_span2);


            var location = $('#LocationGet').val();
            $.ajax({
                url: "{{ url('/trend/analysis/load/data') }}/" + brand + "/" + fromdate_span1 + "/" +
                    todate_span1 +
                    "/" + fromdate_span2 + "/" + todate_span2 + "/" + type,
                type: 'GET',
                dataType: 'json',
                data: {
                    location: location,
                },
                success: function (dataList) {


                    var dates_for_span1 = ['product'];
                    var ounces_for_span_1 = ['Span1'];
                    var ounces_for_span_2 = ['Span2'];


                    if (type == 'ounces') {

                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(parseInt(dataList.span1[i]['Ounces']));
                        }
                        console.log(dataList.span1.length + "---" + dataList.span2.length);

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(parseInt(dataList.span2[i]['Ounces']));
                        }

                    }
                    if (type == 'temperature') {

                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(dataList.span1[i]['AvgTemp']);
                        }

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(dataList.span2[i]['AvgTemp']);
                        }

                    }
                    if (type == 'pressure') {

                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(dataList.span1[i]['AvgPres']);
                        }

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(dataList.span2[i]['AvgPres']);
                        }

                    }


                    var chartDom = document.getElementById('chart-container');
                    var myChart = echarts.init(chartDom);
                    var option;

                    setTimeout(function () {
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

                            legend: {},
                            tooltip: {
                                trigger: 'axis',
                                showContent: false
                            },
                            dataset: {
                                source: [
                                    dates_for_span1,
                                    ounces_for_span_1,
                                    ounces_for_span_2,
                                ]
                            },
                            xAxis: {
                                type: 'category'
                            },
                            yAxis: {
                                gridIndex: 0
                            },
                            series: [{
                                type: 'line',
                                smooth: true,
                                seriesLayoutBy: 'row',
                                emphasis: {
                                    focus: 'series'
                                }
                            },
                                {
                                    type: 'line',
                                    smooth: true,
                                    seriesLayoutBy: 'row',
                                    emphasis: {
                                        focus: 'series'
                                    }
                                }
                            ]
                        };
                        myChart.on('updateAxisPointer', function (event) {
                            const xAxisInfo = event.axesInfo[0];
                            if (xAxisInfo) {
                                const dimension = xAxisInfo.value + 1;
                                myChart.setOption({
                                    series: {
                                        id: 'pie',
                                        label: {
                                            formatter: '{b}: {@[' + dimension +
                                                ']} ({d}%)'
                                        },
                                        encode: {
                                            value: dimension,
                                            tooltip: dimension
                                        }
                                    }
                                });
                            }
                        });
                        myChart.setOption(option, true);
                    });

                    option && myChart.setOption(option);

                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });


        }

        function loadDataOnload(brand, fromdate_span1, todate_span1, fromdate_span2, todate_span2, type) {
            $('#spinner-div').show();

            /*
                        from = new Date(fromdate_span1);
                        to = new Date(todate_span1);

                        diff = new Date(to - from);
                        days = diff / 1000 / 60 / 60 / 24;
                        dayCal = Math.ceil(days);


                        // - 1
                        var date1 = new Date(fromdate_span1);
                        date1.setDate(date1.getDate() - 1);
                        var dateString = date1.toISOString('en-US').slice(0, 16);

                        console.log(dateString);
                        console.log(dayCal);


                        var date = new Date(dateString);
                        date.setDate(date.getDate() - 2);
                        var dateString2 = date.toISOString('en-US').slice(0, 16);
                        console.log(dateString2);


                        $('#todate_span2').val(dateString);
                        $('#fromdate_span2').val(dateString2);

            */

            $.ajax({
                url: "{{ url('/trend/analysis/load/data') }}/" + brand + "/" + fromdate_span1 + "/" +
                    todate_span1 +
                    "/" + fromdate_span2 + "/" + todate_span2 + "/" + type,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {

                    var dates_for_span1 = ['product'];
                    var ounces_for_span_1 = ['Span1'];
                    var ounces_for_span_2 = ['Span2'];


                    if (type == 'ounces') {


                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(parseInt(dataList.span1[i]['Ounces']));
                        }

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(parseInt(dataList.span1[i]['Ounces']));
                        }

                    }
                    if (type == 'temperature') {

                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(dataList.span1[i]['AvgTemp']);
                        }

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(dataList.span2[i]['AvgTemp']);
                        }

                    }
                    if (type == 'pressure') {

                        for (var i = 0; i < dataList.span1.length; i++) {
                            stDate = "Day ";
                            dat = i + 1;
                            dates_for_span1.push(stDate + dat);
                            ounces_for_span_1.push(dataList.span1[i]['AvgPres']);
                        }

                        for (var i = 0; i < dataList.span2.length; i++) {
                            ounces_for_span_2.push(dataList.span2[i]['AvgPres']);
                        }

                    }


                    var chartDom = document.getElementById('chart-container');
                    var myChart = echarts.init(chartDom);
                    var option;

                    setTimeout(function () {
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
                            legend: {},
                            tooltip: {
                                trigger: 'axis',
                                showContent: false
                            },
                            dataset: {

                                source: [
                                    dates_for_span1,
                                    ounces_for_span_1,
                                    ounces_for_span_2,
                                ]
                            },
                            xAxis: {
                                type: 'category'
                            },
                            yAxis: {
                                gridIndex: 0
                            },
                            series: [{
                                type: 'line',
                                smooth: true,
                                seriesLayoutBy: 'row',
                                emphasis: {
                                    focus: 'series'
                                }
                            },
                                {
                                    type: 'line',
                                    smooth: true,
                                    seriesLayoutBy: 'row',
                                    emphasis: {
                                        focus: 'series'
                                    }
                                }
                            ]
                        };
                        myChart.on('updateAxisPointer', function (event) {
                            const xAxisInfo = event.axesInfo[0];
                            if (xAxisInfo) {
                                const dimension = xAxisInfo.value + 1;
                                myChart.setOption({
                                    series: {
                                        id: 'pie',
                                        label: {
                                            formatter: '{b}: {@[' + dimension +
                                                ']} ({d}%)'
                                        },
                                        encode: {
                                            value: dimension,
                                            tooltip: dimension
                                        }
                                    }
                                });
                            }
                        });
                        myChart.setOption(option, true);
                    });

                    option && myChart.setOption(option);

                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });

        }
    </script>

@endsection
