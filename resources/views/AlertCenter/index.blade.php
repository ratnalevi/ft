@extends('layouts.master')

@section('title', 'Alert Center')
@section('content')

    <style>
        i.fa.fa-plus,
        i.fa.fa-pencil-square-o {
            font-size: 22px;
        }

        .form-control1 {
            /* display: block; */
            width: 42%;
            height: 40px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #4d5875;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e1e5ef;
            border-radius: 3px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
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

        .table-container {
            overflow: auto;
            max-height: 500px;
            /* adjust the maximum height as needed */
        }

        .tableFixHead thead {
            position: sticky;
            top: 0;
            background-color: rgb(255, 255, 255);

        }
    </style>

    <div class="col-lg-12">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fa fa-bell" aria-hidden="true"></i>
                <span class="ml-2">Alerts Center</span>

            </h4>
        </div>

        <div class="card">
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Action(s)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($alertList as $item)
                                @php

                                    $dateTime = new DateTime($item->AlertDateTime);
                                    $timeInAmPm = $dateTime->format('m-d-y h:i A'); // outputs "04:30 PM" for the given datetime
                                    $timeForPour = $dateTime->format('m/d/Y H:i:s'); // outputs "04:30 PM" for the given datetime
                                @endphp
                                <tr>
                                    <td style="width: 250px;">{{ $timeInAmPm }}</td>

                                    <td style="width: 550px;">
                                        @if ($item->AlertDescription == 'Temperature is high:')
                                            <b>{{ $item->LocationName }} - </b>
                                            Line {{ $item->Line }} <span> </span>
                                            {{ '(' . $item->Brand . ') : ' . $item->describe }} Alert<span><br>
                                                </span>
                                            <b>Highest Temperature Recorded: </b>{{ $item->max_value }} (F)<br>
                                            <b>Lowest Temperature Recorded: </b>{{ $item->min_value }} (F)
                                            <br> <b>Total {{ $item->describe }} Alerts for this line:
                                            </b>{{ $item->AlertCNT }}<br>
                                        @elseif ($item->AlertDescription == 'Pressure is high:')
                                            <b>{{ $item->LocationName }} - </b>
                                            Line {{ $item->Line }} <span> </span>
                                            {{ '(' . $item->Brand . ') : ' . $item->describe }} Alert<span><br>
                                                </span>
                                            <b>Highest Pressure Recorded: </b>{{ $item->max_value }} (PSI)<br>
                                            <b>Lowest Pressure Recorded: </b>{{ $item->min_value }} (PSI)
                                            <br> <b>Total {{ $item->describe }} Alerts for this line:
                                            </b>{{ $item->AlertCNT }}<br>
                                            {{-- {{ $item->describe }} <span> </span> {{ $item->PressAlertValue }} <span>
                                            (PSI) </span> --}}
                                        @elseif ($item->AlertDescription == 'A pour was detected outside allowable hours:')
                                            <b>{{ $item->LocationName }} - </b>
                                            Line {{ $item->Line }} <span> </span>
                                            {{ '(' . $item->Brand . ') : ' . $item->describe }} Alert<span><br>
                                                </span>
                                            <b>Time: </b>{{ $timeInAmPm }}
                                            <br>
                                            {{-- {{ $item->describe }} <span> </span> {{ $item->PressAlertValue }} <span>
                                            (PSI) </span> --}}
                                        @else
                                            {{ $item->Name }} <br>
                                            Line {{ $item->Line }} <span> </span>
                                            {{ '(' . $item->Brand . ') ' }}<span>
                                                </span>
                                            {{ $item->describe }}
                                            {{-- <span> </span> {{ $item->PressAlertValue }} <span>
                                                PSI </span> --}}
                                        @endif

                                        {{-- {{ $item->Line }} {{ '(' . $item->Brand . ')' }}
                                    {{ $item->describe }} {{ $item->AlertDateTime }} <br> --}}
                                    </td>
                                    <td>
                                        <button
                                            onclick="AckAction({{ $item->DevicesID }},{{ $item->Line }},{{ $item->DeviceLinesAlertCurrentID }})"
                                            class="btn btn-primary btn-sm">OK
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @php
            $names = Session::get('userID');
            $name = DB::table('UserDemographic')
                ->where('UserID', $names->UserID)
                ->first();
        @endphp

        <div class="card">

            <div class="row m-2 mt-4">
                <div class="col-lg-3">
                    <select style="border-radius: 36px;" name="alertType" id="alertType" class="alertType form-control">
                        @foreach ($alert as $item)
                            <option value="{{ $item->AlertID }}">{{ $item->AlertName }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-lg-4">
                    <select class="form-control" style="border-radius: 36px;" name="locationId" id="locationId">
                        @foreach ($locationsUsers as $items)
                            @if ($items->LocationID == $name->LocationID)
                                <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}
                                </option>
                            @else
                                <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                            @endif
                        @endforeach
                    </select>
                </div> --}}
                @php
                    $dateSetFrom6AMString = date('Y-m-d\T06:00', time());
                    $currentDate = date('Y-m-d\T06:00', time());

                    $minDateTime = new DateTime();
                    $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                    $currentDateTime = new DateTime();
                    $maxDateString = $currentDateTime->format('Y-m-d\TH:i'); // Set the ending year to 2023
                @endphp
                <div class="col-lg-3">
                    <input style="margin: 0 25px 0 0; border-radius: 36px;" id="fromdate" type="datetime-local"
                           class="form-control"
                           value="{{ $dateSetFrom6AMString }}" min="{{ $minDateString }}" max="{{ $maxDateString }}">
                </div>
                {{-- </div>
            <div class="row m-2 mt-4"> --}}

                <div class="col-lg-3">
                    <input type="datetime-local" style="border-radius: 36px;" id="todate" class="form-control"
                           value="{{ $currentDate }}" min="{{ $minDateString }}" max="{{ $maxDateString }}">
                </div>
                <div class="col-lg-1">
                    <a id="refreshBtn" href="#" class="mt-4">
                        <i class="fa fa-refresh" aria-hidden="true"
                           style="font-size: 100%;float: right;
                        padding-top: 11px;"> Refresh
                        </i>
                    </a>
                </div>
            </div>

            <div class="table-responsive mt-5">
                <div class="table-container">
                    <table class="table tableFixHead">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Alert</th>
                            <th>User Ack</th>
                            <th>User Name</th>
                            <th>Ack Date</th>
                        </tr>
                        </thead>
                        <tbody id="dataList">

                        </tbody>
                    </table>
                </div>
                <ul id="pagination" class="pagination justify-content-end mr-2 mt-2">
                    <!-- Pagination links will be appended here -->
                </ul>
                <div id="spinner-div" class="pt-5" style="display: none;">
                    <div style="margin-top: 13rem !important;" class="mt-5">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function changeExipry(expiry) {
            $('#spinner-div').show();
            $.ajax({
                url: "/update-alert-expiry",
                type: 'GET',
                data: data = {
                    'expiry': expiry,
                },
                success: function (res) {
                    console.log(res);
                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });
        }

        var fromDate = 'fromdate';
        var toDate = 'todate';

        $(document).ready(function () {
            window.addEventListener("DOMContentLoaded", () => {
                const tableContainer = document.querySelector(".table-container");
                const table = document.querySelector("#myTable");

                // Set table height dynamically
                tableContainer.style.maxHeight = (window.innerHeight - tableContainer.offsetTop) + "px";

                // Recalculate table height when the window is resized
                window.addEventListener("resize", () => {
                    tableContainer.style.maxHeight = (window.innerHeight - tableContainer
                        .offsetTop) + "px";
                });
            });

            // Calculate the start and end dates for the default date range
            var nowDate = new Date();
            var startDate = new Date();
            var endDate = new Date();

            startDate.setHours(6, 0, 0, 0); // Set start time to 6:00 AM in Arizona

            if (nowDate > startDate) {
                //at the moment 6 am has passed so we are just going to add 1 in end date

                endDate.setDate(endDate.getDate() + 1); // Add 1 day
            } else {
                startDate.setDate(startDate.getDate() - 1);
            }

            endDate.setHours(6, 0, 0, 0); // Set end time to 6:00 AM in Arizona

            // Format the dates for display
            var formattedStartDate = startDate.toLocaleString('en-US', {
                month: 'numeric',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });

            var formattedEndDate = endDate.toLocaleString('en-US', {
                month: 'numeric',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            });

            var to = $('#todate').val(formatDateForInput(formattedEndDate));
            var from = $('#fromdate').val(formatDateForInput(formattedStartDate));

            //function for date conversion to current time zone
            function convertTZ(date, tzString) {
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    timeZone: tzString,
                    timeZoneName: 'short'
                }
                const formatter = new Intl.DateTimeFormat('sv-SE', options)
                const startingDate = new Date(date)

                return formatter.format(startingDate);
            }

            function formatDateForInput(dateString) {
                var date = new Date(dateString);
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                var hours = date.getHours().toString().padStart(2, '0');
                var minutes = date.getMinutes().toString().padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }

        });

        $(document).ready(function () {

            $('#refreshBtn').on('click', function () {
                var alertType = $('#alertType').val();
                var fromdate = $('#fromdate').val();
                var todate = $('#todate').val();
                ajaxLoadData(alertType, fromdate, todate, 1);
            });

            var alertType = $('#alertType').val();
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();
            var currentPage = 1; // Initial page
            ajaxLoadData(alertType, fromdate, todate, currentPage);

            function ajaxLoadData(alertType, fromdate, todate, page, location) {
                $('#spinner-div').show();
                $('#dataList').html("");
                $.ajax({
                    url: "/alert-center-data",
                    type: 'GET',
                    data: {
                        'alertType': alertType,
                        'fromdate': fromdate,
                        'location': location,
                        'todate': todate,
                        'page': page
                    },
                    success: function (res) {
                        console.log(res);
                        var option = '';
                        console.log(typeof res);
                        res.data.forEach(element => {
                            if (element.AckDateTime) {
                                var status = "OK";
                            } else {
                                var status = "SYS";
                            }
                            option += '<tr>';
                            option += '<td>' + element.AlertDateTime + '</td>';
                            // option += '<td>' + element.Name + '<br> Line' + element.Line +
                            if (element.AlertID == 1) {
                                option += '<td> Line ' + element.Line +
                                    '<span> </span>(' + element.Brand + ') : ' + element
                                        .describe + ' Alert <span> </span>';
                                // option += ' Temperature Alert <br>';
                                option += ' <br><b>Highest Temperature Recorded:</b> ' + element
                                        .max_value +
                                    ' (F)<br>';
                                option += ' <b>Lowest Temperature Recorded:</b> ' + element
                                        .min_value +
                                    ' (F)';
                                option += '<br><b>Total ' + element.describe +
                                    ' Alerts for this line:</b> ' +
                                    element.AlertCNT + '<br></td>';
                            } else if (element.AlertID == 2) {
                                option += '<td> Line ' + element.Line +
                                    '<span> </span>(' + element.Brand + ') : ' + element
                                        .describe + ' Alert<span> </span>';
                                option += ' <br><b>Highest Pressure Recorded:</b> ' + element
                                        .max_value +
                                    ' (PSI)<br>';
                                option += ' <b>Lowest Pressure Recorded:</b> ' + element
                                        .min_value +
                                    ' (PSI)';
                                option += '<br><b>Total ' + element
                                        .describe + ' Alerts for this line:</b> ' +
                                    element.AlertCNT + '<br></td>';
                            } else if (element.AlertID == 3) {
                                option += '<td> Line ' + element.Line +
                                    '<span> </span>(' + element.Brand + ') : ' + element
                                        .describe + ' Alert<span> </span>';
                                option += ' <br><b>Time:</b> ' + element
                                    .AlertDateTime;
                                option += '<br><b>Total ' + element
                                        .describe + ' Alerts for this line:</b> ' +
                                    element.TotalAlertCNT + '<br></td>';
                            } else {

                                option += '<td><b> Line ' + element.Line +
                                    ',</b><span> </span>' + element.Brand + ':<span> </span>';
                                option += element.describe +
                                    ' <span> </span></td > ';

                            }
                            option += '<td>' + status + '</td>';
                            if (element.FirstName) {
                                option += '<td>' + element.FirstName + ' ' + element.LastName +
                                    '</td>';
                            } else {
                                option += '<td>--</td>';

                            }
                            option += '<td>' + element.AckDateTime + '</td>';
                            // option += '<td>--</td>';
                            // option += '<td>--</td>';
                            option += '</tr>';
                        });
                        $('#dataList').append(option);
                        var pagination = '';
                        if (res.prev_page_url) {
                            pagination +=
                                '<li class="page-item"><a class="page-link" href="#" data-page="' + (res
                                    .current_page - 1) + '"><</a></li>';
                        }

                        for (var i = 1; i <= res.last_page; i++) {
                            pagination += '<li class="page-item ' + (i === res.current_page ? 'active' :
                                    '') + '"><a class="page-link" href="#" data-page="' + i + '">' + i +
                                '</a></li>';
                        }

                        if (res.next_page_url) {
                            pagination +=
                                '<li class="page-item"><a class="page-link" href="#" data-page="' + (res
                                    .current_page + 1) + '">></a></li>';
                        }

                        $('#pagination').html(pagination);
                    },
                    complete: function () {
                        $('#spinner-div').hide();
                    }

                });
            }

            $(document).on('click', '#pagination a', function (e) {
                e.preventDefault();
                var page = $(this).data('page');
                var alertType = $('#alertType').val();
                var fromdate = $('#fromdate').val();
                var todate = $('#todate').val();
                ajaxLoadData(alertType, fromdate, todate, page);
            });
        });

        function AckAction(id, line, DeviceLinesAlertCurrentID) {

            var formattedTime = new Date();
            var formattedTimeoptions = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            };

            var formattedTimeD = formattedTime.toLocaleString('en-US', formattedTimeoptions);
            $('#spinner-div').show();

            $.ajax({
                url: "/ack-data",
                type: 'GET',
                data: data = {
                    'id': id,
                    'line': line,
                    'DeviceLinesAlertCurrentID': DeviceLinesAlertCurrentID,
                    'date': formattedTimeD,
                },
                success: function (res) {
                    if (res.status == '1') {
                        toastr.options = {
                            "closeButton": true,
                            "progressBar": true
                        }
                        toastr.success("Acknowledged");
                        location.reload();
                    }
                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });
        }
    </script>
@endsection
