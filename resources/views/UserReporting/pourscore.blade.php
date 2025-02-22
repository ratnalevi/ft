@extends('layouts.master')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>

@section('title', 'Home')
@section('content')

    <style>
        #spinner-div {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 70px;
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
                <div class="col-lg-12">
                    <h5>
                        <i class="fa fa-window-restore" aria-hidden="true"></i>
                        <span class="ml-3">PourScore Tracking By Location</span>
                    </h5>
                </div>
                <div class="col-lg-12">
                    <a id="refreshBtn" href="#">
                        <i class="fa fa-refresh" aria-hidden="true" style="font-size: 100%;float: right;"> Refresh
                        </i>
                    </a>
                </div>
                <div style="display: flex" class="col-lg-12">
                    @php
                        $names = Session::get('userID');
                        $name = DB::table('UserDemographic')
                            ->where('UserID', $names->UserID)
                            ->first();
                    @endphp

                    <div style="padding: 0px;" class="col-lg-3 mt-3">
                        <select class="form-control location" style="height: 49px !important;border-radius: 36px"
                                onchange="getval(this);" name="locations" id="location">
                            @foreach ($locations as $items)
                                @if ($items->LocationID == $name->LocationID)
                                    <option value="{{ $items->LocationID }}"
                                            selected>{{ $items->LocationName }}</option>
                                @else
                                    <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                    <div class="col-lg-3  mt-3">
                        <select class="form-control types" style="height: 49px !important;border-radius: 36px"
                                name="types" id="types">
                            <option value="ounces">PourScore by Ounces</option>
                            <option value="revenue">PourScore by Revenue</option>
                        </select>
                    </div>
                    @php
                        $dateFormat = 'Y-m-d 06:00';
                        $timeZone = 'America/New_York';
                        $currentDate = new DateTime('now', new DateTimeZone($timeZone));

                        $newStartDate = $currentDate->format($dateFormat);
                        $newEndDate = $currentDate->sub(new DateInterval('P1D'))->format($dateFormat);

                        $minDateString = new DateTime('now', new DateTimeZone($timeZone));
                        $minDateString = $minDateString->sub(new DateInterval('P3M'))->format($dateFormat);
                        $maxDateString = new DateTime('now', new DateTimeZone($timeZone));
                        $maxDateString = $maxDateString->format($dateFormat);
                    @endphp
                    <div class="col-lg-3 mt-3">
                        <input style="height: 49px !important; margin: 0px 25px 0px 0px; border-radius: 36px;"
                               id="startDate" type="datetime-local" class="form-control" value="{{ $newEndDate }}"
                               min="{{ $minDateString }}" max="{{ $maxDateString }}">
                    </div>

                    <div style="padding: 0px;" class="col-lg-3 mt-3">
                        <input type="datetime-local" style="height: 49px !important; border-radius: 36px;" id="endDate"
                               class="form-control" value="{{ $newStartDate }}" min="{{ $minDateString }}"
                               max="{{ $maxDateString }}">
                    </div>
                </div>

                <!-- row -->
                <div class="col-md-12" style="margin-top: 1%;">
                    <div class="card mg-b-20">
                        <div class="card-body">
                            <div class="main-content-label mg-b-5 pt-2pb-5 text-center location_by_ounces_poured"
                                 style="display: none;">
                                PourScore by Brand and Ounces
                            </div>
                            <div class="main-content-label mg-b-5 pt-2pb-5 text-center location_by_revenue"
                                 style="display: none;">
                                PourScore by Brand and Revenue
                            </div>
                            <div id="spinner-div" class="pt-5">
                                <div class="mt-5">
                                    <div class="spinner-border text-primary" role="status">
                                    </div>
                                </div>
                            </div>
                            <div id="chart-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.4.1/dist/echarts.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            LoadPourScore();
        });

        function clearChartContainer() {
            const dom = document.getElementById('chart-container');
            const myChart = echarts.getInstanceByDom(dom);
            if (myChart) {
                myChart.dispose(); // Dispose the chart instance to remove the old chart
            }
            $('#chart-container').empty(); // Clear the chart container content
        }

        $('#refreshBtn').on('click', function () {
            LoadPourScore();
        });

        $('#types').on('change', function () {
            var type = this.value;
            var symbol;

            if (this.value === 'ounces') {
                $('.location_by_ounces_poured').css('display', 'block');
                $('.location_by_revenue').css('display', 'none');
            }

            if (this.value === 'revenue') {
                symbol = 'Amount $';
                $('.location_by_ounces_poured').css('display', 'none');
                $('.location_by_revenue').css('display', 'block');
            }

            LoadPourScore();
        });

        $('#daysfilter').on('change', function () {
            var symbol;
            var type = $('#types').val();

            if (type === 'pourscore') {
                symbol = 'Percentage %';
            }
            if (type === 'ounces') {
                symbol = 'Ounces OZ';
            }
            if (type === 'revenue') {
                symbol = 'Amount $';
            }

            LoadPourScore();
        });


        function getval(sel) {
            LoadPourScore();
        }

        function getGraphSymbol()
        {
            let symbol = '';
            let type = $("#types").val();

            if (type === 'pourscore') {
                symbol = '%';
            }
            if (type === 'ounces') {
                symbol = 'OZ';
            }
            if (type === 'revenue') {
                symbol = '$';
            }

            return symbol;
        }

        function LoadPourScore() {
            const location = $("#location").val();
            const startDate = $("#startDate").val();
            const endDate = $("#endDate").val();
            const type = $("#types").val();

            $('#spinner-div').show(); //Load button clicked show spinner
            clearChartContainer();
            $.ajax({
                url: "{{ url('/pourscore-report') }}/" + location + "/" + type,
                type: 'GET',
                data: {
                    startDate: startDate,
                    endDate: endDate
                },
                dataType: 'json',
                success: function (dataList) {
                    seriesItemName = [];
                    seriesOunces = [];
                    $.each(dataList, function (index, value) {
                        seriesItemName.push(value.ItemName);
                        seriesOunces.push(value.data);
                    });

                    var dom = document.getElementById('chart-container');
                    var myChart = echarts.init(dom, null, {
                        renderer: 'canvas',
                        useDirtyRect: false
                    });

                    var option = {
                        legend: {
                            top: 0,
                        },
                        grid: {
                            top: '20%',
                            left: '3%',
                            right: '4%',
                            bottom: '5%',
                            containLabel: true
                        },
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
                                rotate: -45, // Adjust the rotation angle
                                fontSize: 12, // Adjust the font size
                                // formatter: function(value, index) {
                                //     if (value.length > 10) {
                                //         return value.substr(0, 10) + '...';
                                //     } else {
                                //         return value;
                                //     }
                                // }
                            },
                            data: seriesItemName,
                        },
                        yAxis: {
                            type: 'value',
                            axisLabel: {
                                formatter: '{value}%',
                            }
                        },
                        series: [{
                            data: seriesOunces,
                            type: 'bar',
                            showBackground: true,
                            // barWidth: 160,
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
                            // console.log(dataIndex);
                            return ' ' + value;
                        }
                    };

                    option.dataZoom = [{
                        type: 'slider', // Enable slider for scrollable x-axis
                        show: true,
                        start: 0,
                        end: 100, // Adjust the range as needed
                        xAxisIndex: [0],
                        height: 20, // Height of the slider
                        bottom: 3, // Position of the slider
                        showDetail: true // Hide detail view
                    }];

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
    </script>

@endsection
