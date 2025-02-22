@extends('layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

@section('title', 'Home Latest')
@section('content')

    <style>
        .full-width-column {
            width: 100%;
        }

        #leftbox {
            font-weight: bold;
            text-align: right;
            float: left;
            width: 16%;
            font-size: 1.12em;
        }

        #estimaated_key_vol {
            font-weight: bold;
            font-size: 1.00em;
            text-align: center;
        }

        #lines_available {
            font-weight: bold;
            color: blue;
            text-align: right;
            font-size: 1.00em;
            padding-right: 69px;
        }

        .mt-5 {
            padding-top: 96px;
            padding-left: 96px;
        }

        th {
            cursor: pointer;
        }

        td#totalounces,
        td#totalpints {
            font-weight: bold;
            text-align: center;
            font-size: 1.12em;
        }

        .form-control {
            -webkit-appearance: auto;
            display: block;
            width: 100%;
            height: 40px;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.5;
            color: #4d5875;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #e1e5ef;
            border-radius: 37px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .dataTables_empty {
            display: none;
        }

        i.fa.fa-exclamation-triangle {
            color: red;
        }

        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            background: none;
        }

        .fa-refresh::before {
            content: "\f021";
            padding: 2px;
        }

        table.table-bordered.dataTable thead tr:first-child th,
        table.table-bordered.dataTable thead tr:first-child td {
            border-top-width: 1px;
            text-align: center;
        }

        .fa-refresh:before {
            content: "\f021";
            padding: 10px;
        }

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

        .table thead th,
        .table thead td {
            color: #37374e;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom-width: 1px;
            border-top-width: 0;
            padding: 0 15px 5px;
        }
    </style>
    <div class="col-lg-12">
        <div class="card pb-4">
            <div class="row m-3">
                <div class="col-lg-10">
                    <h4 class="mt-3">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                        <span class="ml-2">Pending Alerts</span>
                    </h4>
                </div>
                <div class="col-lg-2">
                    <p class="mt-3" style="color: blue;text-align:right;"><a onclick="gotoAlert()" href="#">Go to
                            Alert Center</a>
                    </p>
                </div>
            </div>


            <div class="row ml-3 ">
                <div class="col-lg-2">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <span class="ml-1" style="color: red">Newest Alert </span> :
                </div>
                <div class="col-lg-9" id="alertsContainer">
                    <!-- The alerts fetched via AJAX will be displayed here -->
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
    <div class="col-lg-12">
        <div class="card">
            <div class="row m-3">
                <div class="col-lg-12">
                    <div class="bs-example">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="mt-3">
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    <span class="ml-3 mr-3">Select a location</span>
                                </h4>
                            </div>
                            <div style="width: 50%;" class="pull-left">
                                <select class="form-control locationLOca mt-2"
                                        style="border-radius: 36px; text-align: left;font-size: 14px; margin-bottom: 7px;"
                                        name="location33" id="ddlFruits">
                                    @foreach ($locationsUsers as $items)
                                        @if ($items->LocationID == $name->LocationID)
                                            <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}
                                            </option>
                                        @else
                                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-6 mt-3">
                </div>
                <div style="margin-bottom: 7px;" class="col-lg-6 mt-2">
                </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="row m-3">
                <div class="col-lg-5 mt-3">
                    <div class="d-flex">
                        <h4 class="mt-2" style="width: 124px;">
                            <span class="ml-1">Device:</span>
                        </h4>
                        <select class="form-control devices" style="border-radius: 36px;font-size: 14px;" name="devices"
                                id="mySelect">
                        </select>
                    </div>
                </div>
                @php
                    $dateSetFrom6AMString = date('Y-m-d\T06:00', time());
                    $currentDate = date('Y-m-d\T06:00', time());

                    $minDateTime = new DateTime();
                    $minDateString = $minDateTime->sub(new DateInterval('P1Y'))->format('Y-m-d\TH:i');

                    $currentDateTime = new DateTime();
                    $maxDateString = $currentDateTime->format('Y-m-d\TH:i');
                @endphp

                <div class="col-lg-3 mt-3">
                    <input style="margin: 0 25px 0 0;" id="fromdate" type="datetime-local" class="form-control"
                           value="{{ $dateSetFrom6AMString }}" min="{{ $minDateString }}" max="{{ $maxDateString }}">
                </div>

                <div class="col-lg-3 mt-3">
                    <input type="datetime-local" id="todate" class="form-control" value="{{ $currentDate }}"
                           min="{{ $minDateString }}" max="{{ $maxDateString }}">
                </div>

                <div class="col-lg-1 mt-4" style="padding: 0;">
                    <a id="refreshBtn" href="#">
                        <i class="fa fa-refresh" aria-hidden="true"
                           style="margin-top: 3%; font-size: 100%;float: right;">Refresh
                        </i>
                    </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <div id="spinner-div" class="pt-5">
                    <div class="mt-5">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
                <style>
                    tbody,
                    td,
                    tfoot,
                    th,
                    thead,
                    tr {
                        border-color: inherit;
                        border-style: solid;
                        border-width: 0;
                        font-size: 14px;
                    }

                    .table-container {
                        overflow: auto;
                        max-height: 500px;
                        /* adjust the maximum height as needed */
                    }

                    .tableFixHead thead {
                        position: sticky;
                        top: 0;
                        background-color: #ffffff;
                        /* adjust the background color as needed */
                    }
                </style>
                <div class="table-container">
                    <table id="myTable" class="table tableFixHead">
                        <thead>
                        <tr>
                            <th style="text-align: center;" class="sort">LINE</th>
                            <th style="text-align: left;" class="sort">BRAND</th>
                            <th style="text-align: center;" class="sort">OUNCES</th>
                            <th style="text-align: center;" class="sort">PINTS</th>
                            <th style="text-align: center;" class="sort">AVG TEMP</th>
                            <th style="text-align: center;" class="sort">MAX TEMP</th>
                            <th style="text-align: center;" class="sort">AVG PRES</th>
                            <th style="text-align: center;" class="sort">MAX PRES</th>
                            <th style="text-align: center;" class="sort">AVG TDS</th>
                            <th style="text-align: left; width: 180px;" class="sort">LAST POUR</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyid">

                        </tbody>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th>Total<b></b></th>
                            <th style="text-align: center;" id="totalounces">0.00<b></b></th>
                            <th style="text-align: center;" id="totalpints">0.00<b></b></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Total Kegs<b></b></th>
                            <th style="text-align: center;" id="estimaated_key_vol">0<b></b></th>
                            <th style="text-align:right; padding-right:20px;" id="lines_available" colspan="7"><b>0
                                    lines available on this device</b>
                            </th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        var fromDate = 'fromdate';
        var toDate = 'todate';

        function gotoAlert() {
            const location_id = $('#ddlFruits').val();
            if (location_id) {
                window.location.href = '/alert-center?location_id=' + location_id;
            } else {
                alert('Please select a location before proceeding.');
            }
        }

        $(document).ready(function () {
            $('#myTable').DataTable({
                paging: false,
                info: false,
                searching: false,
                order: [
                    [0, 'asc']
                ],
                order: [], // Disable initial sorting
                columns: [{
                    "orderable": false
                }, // Disable sorting for the first column (index 0)
                    {
                        "orderable": false
                    }, // Disable sorting for the second column (index 1)
                    {
                        "orderable": false
                    }, // Disable sorting for the third column (index 2)
                    {
                        "orderable": false
                    }, // Enable sorting for the remaining columns
                    {
                        "orderable": false
                    },
                    {
                        "orderable": false
                    },
                    {
                        "orderable": false
                    },
                    {
                        "orderable": false
                    },
                    {
                        "orderable": false
                    },
                    {
                        "orderable": false
                    }
                ]
            });
        });
    </script>

    <script>
        // End Sorting
        $(document).ready(function () {
            $("#currentPage").val(1);
            $(".btn-decrement").prop('disabled', true);

            var device = $("#mySelect").val()
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();

            $("#lines_available").html("0 lines available on this device")
            $("#total_pourecode").html("");
            $("#estimaated_key_vol").html("");


            $('#totalpints').html("");
            $('#totalounces').html("");
        });

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

            $('#todate').val(formatDateForInput(formattedEndDate));
            $('#fromdate').val(formatDateForInput(formattedStartDate));

            $('.sort').click(function () {
                var table = $(this).parents('table').eq(0);
                var tbody = table.find('tbody');
                var rows = tbody.find('tr').toArray().sort(comparer($(this).index()));
                this.asc = !this.asc;
                if (!this.asc) {
                    rows = rows.reverse();
                }
                for (var i = 0; i < rows.length; i++) {
                    tbody.append(rows[i]);
                }
            });

            function comparer(index) {
                return function (a, b) {
                    if (index == 2 || index == 3) {
                        var valA = Number(getCellValue(a, index).replace(/,/g, ''));
                        var valB = Number(getCellValue(b, index).replace(/,/g, ''));
                    } else {
                        var valA = getCellValue(a, index);
                        var valB = getCellValue(b, index);
                    }
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
                };
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }
        });

        // End Sorting
        $(document).ready(function () {
            $("#currentPage").val(1);
            $(".btn-decrement").prop('disabled', true);

            var device = $("#mySelect").val()
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();

            $("#lines_available").html("0 lines available on this device")
            $("#total_pourecode").html("");
            $("#estimaated_key_vol").html("0.00");

            $('#totalpints').html("0");
            $('#totalounces').html("0");
        });

        function changePage(value) {
            var lastPage = $("#lastPage").val();
            if (value < 1) {
                $("#currentPage").val(0);
                $(".btn-decrement").prop('cursor', 'not-allowed');
                $(".btn-decrement").prop('disabled', true);
            } else {
                $(".btn-decrement").prop('disabled', false);
                //update current page value
                var newPage = 1;
                var device = $("#mySelect").val();

                var fromdate = $('#fromdate').val();
                var todate = $('#todate').val();

                $("#lines_available").html("0 lines available on this device")
                $("#total_pourecode").html("");
                $("#estimaated_key_vol").html("");


                $('#totalpints').html("");
                $('#totalounces').html("");

                loadData(device, fromdate, todate, newPage);
            }

            if (value === 1) {
                $(".btn-decrement").prop('cursor', 'not-allowed');
                $(".btn-decrement").prop('disabled', true);
            } else {
                $(".btn-decrement").prop('cursor', 'pointer');
                $(".btn-decrement").prop('disabled', false);
            }
            if (value === lastPage) {
                $(".btn-increment").prop('cursor', 'not-allowed');
                $(".btn-increment").prop('disabled', true);
            } else {
                $(".btn-increment").prop('cursor', 'pointer');
                $(".btn-increment").prop('disabled', false);
            }

        }

        ajaxLocation();

        function ajaxLocation() {

            $("#total_pourecode").html("");
            $("#estimaated_key_vol").html("");


            $('#totalpints').html("");
            $('#totalounces').html("");

            $("#tbodyid").empty();

            var location_id = $('#ddlFruits').val();

            $.ajax({
                url: "{{ url('/load/devices') }}/" + location_id,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    const mySelect = $('#mySelect');
                    mySelect.empty();
                    for (let i = 0; i < dataList.length; i++) {
                        mySelect.append($('<option>', {
                            value: dataList[i]['DevicesID'],
                            text: dataList[i]['Name']
                        }));
                    }

                    //setting the time here cause this function gets called early
                    // Calculate the start and end dates for the default date range
                    const nowDate = new Date();
                    const startDate = new Date();
                    const endDate = new Date();

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

                    $('#todate').val(formatDateForInput(formattedEndDate));
                    $('#fromdate').val(formatDateForInput(formattedStartDate));

                    var device = $("#mySelect").val()
                    var fromdate = $('#fromdate').val();
                    var todate = $('#todate').val();

                    $("#lines_available").html("0 lines available on this device")
                    $("#total_pourecode").html("");
                    $("#estimaated_key_vol").html("");


                    $('#totalpints').html("");
                    $('#totalounces').html("");

                    var as = 1;
                    console.log(fromdate + "--" + todate);
                    loadData(device, fromdate, todate, as);

                },
            });
        }

        function getalerts() {
            const location = $("#ddlFruits").val();
            $.ajax({
                url: '/getHomeAlerts/' + location,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    var alertsContainer = $("#alertsContainer");
                    alertsContainer.empty(); // Clear existing alerts
                    $.each(data, function (index, item) {
                        if (item.AlertDescription === 'Temperature is high:') {
                            var alertHTML = '<b>' + item.LocationName + ' - </b>' +
                                'Line ' + item.Line + ' (' + item.Brand + '): ' + item.describe;
                            // alertHTML += ' Temperature Alert <br>';
                            alertHTML += ' Alert <br><b>Highest Temperature Recorded:</b> ' + item
                                    .max_value +
                                ' (F)<br>';
                            alertHTML += ' <b>Lowest Temperature Recorded:</b> ' + item.min_value +
                                ' (F)';
                            alertHTML += '<br><b>Total ' + item.describe +
                                ' ALerts for this line:</b> ' + item.AlertCNT +
                                '<br><br>';
                            // alertHTML += item.describe + ' ' + item.TempAlertValue + ' (F)<br><br>';
                        } else if (item.AlertDescription === 'Pressure is high:') {
                            var alertHTML = '<b>' + item.LocationName + ' - </b>' +
                                'Line ' + item.Line + ' (' + item.Brand + '): ' + item.describe;
                            // alertHTML += ' Pressure Alert <br>';
                            alertHTML += ' Alert <br><b>Highest Pressure Recorded:</b> ' + item
                                    .max_value +
                                ' (PSI)<br>';
                            alertHTML += ' <b>Lowest Pressure Recorded:</b> ' + item.min_value +
                                ' (PSI)';
                            alertHTML += '<br><b>Total ' + item.describe +
                                ' Alerts for this line:</b> ' + item.AlertCNT +
                                '<br><br>';
                            // alertHTML += item.describe + ' ' + item.PressAlertValue +
                            //     ' (PSI)<br><br>';
                        } else if (item.AlertDescription ===
                            'A pour was detected outside allowable hours:') {
                            var alertHTML = '<b>' + item.LocationName + ' - </b>' +
                                'Line ' + item.Line + ' (' + item.Brand + '): ' + item.describe +
                                ' Alert';
                            alertHTML += '<br><b>Time: </b> ' + item.AlertDateTime + ' <br>';
                        } else {
                            var alertHTML = '<b>' + item.Name + '</b><br>' +
                                'Line ' + item.Line + ' (' + item.Brand + ') ';
                            alertHTML += item.describe + ' <br><br>';

                        }

                        alertsContainer.append(alertHTML);
                    });
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        }

        $('#ddlFruits').on('change', function () {
            ajaxLocation();
            getalerts();
        });

        getalerts();

        $('#refreshBtn').on('click', function () {
            $("#currentPage").val(1);
            var page = 1;
            var device = $("#mySelect").val();
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();

            $("#lines_available").html("0 lines available on this device")
            $("#total_pourecode").html("0.00");
            $("#estimaated_key_vol").html("0.00");

            $('#totalpints').html("");
            $('#totalounces').html("");

            loadData(device, fromdate, todate, page);
            $("#tbodyid").empty();
        });

        function loadData(device, fromdate, todate, newPage) {
            $("#total_pourecode").html("0.00");
            $("#estimaated_key_vol").html("0.00");
            $('#totalpints').html("0.00");
            $('#totalounces').html("0.00");

            $('#spinner-div').show(); //Load button clicked show spinner
            $.ajax({
                url: "{{ url('/load/line/data') }}/" + device + "/" + fromdate + "/" + todate + "/" + newPage,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    $("#currentPage").val(newPage);
                    $("#tbodyid").empty();

                    // $('.table').DataTable().clear().destroy();
                    let $totalPints = 0;
                    let $totalOunces = 0;
                    const dt = new Date();
                    let diffTZ = Intl.DateTimeFormat().resolvedOptions().timeZone;

                    for (var i = 0; i < dataList.result.length; i++) {
                        var time = dataList.result[i]['LastPourTime'];
                        var row = "<tr>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['Line'] + "</td>" +
                            "<td>" + dataList.result[i]['Brand'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['Ounces'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['Pints'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['AvgTemp'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['MaxTemp'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['AvgPres'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['MaxPres'] + "</td>" +
                            "<td style='text-align:center;'>" + dataList.result[i]['AvgTDS'] + "</td>" +
                            "<td>" + time + "</td>" +
                            "</tr>";
                        $("#tbodyid").append(row);
                        $totalPints = $totalPints + parseInt(dataList.result[i]['Pints'].toString().replace(',', ''));
                        $totalOunces = $totalOunces + parseInt(dataList.result[i]['Ounces'].toString().replace(',', ''));
                    }

                    $('#totalpints').html("" + $totalPints.toLocaleString("en-US"));
                    $('#totalounces').html("" + $totalOunces.toLocaleString("en-US"));
                    $('#info').html('');
                    var info = '<p style="margin-left: 1%;">' + dataList.from + ' to ' + dataList.to +
                        ' of ' +
                        dataList.total + '</p>';
                    $("#info").html(info);
                    $("#lastPage").val(dataList.lastPage);
                    $("#lines_available").text(parseInt(dataList.result.length) +
                        " lines available on this device")
                    $("#total_pourecode").text(dataList.totalpourCode);
                    $("#estimaated_key_vol").text(($totalOunces / 1984).toFixed(2).toLocaleString("en-US"));

                    if (dataList.formattedFromDate !== "") {
                        $('#todate').val(formatDateForInput(dataList.formattedToDate));
                        $('#fromdate').val(formatDateForInput(dataList.formattedFromDate));
                    }
                },
                complete: function () {
                    $('#spinner-div').hide(); //Request is complete so hide spinner
                }
            });
        }

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
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    </script>
@endsection
