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
        .mt-5 {
            padding-top: 96px;
            padding-left: 96px;
        }

        th {
            cursor: pointer;
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
                    $dateFormat = 'Y-m-d\T06:00';
                    $timestamp = time();
                    $currentDate = date($dateFormat, $timestamp);
                    $CurrentDate = date($dateFormat);
                    $newStartDate = date($dateFormat, strtotime($currentDate . ' -7 day'));
                    $newEndDate = date($dateFormat, strtotime($currentDate . ' +1 day'));

                    $minDateTime = new DateTime();
                    $minDateString = $minDateTime->sub(new DateInterval('P6M'))->format($dateFormat);

                    $currentDateTime = new DateTime();
                    $maxDateString = $currentDateTime->format($dateFormat);
                @endphp

                <div class="col-lg-3 mt-3">
                    <input style="height: 49px !important; margin: 0px 25px 0px 0px; border-radius: 36px;"
                           id="fromdate" type="datetime-local" class="form-control" value="{{ $newStartDate }}"
                           min="{{ $minDateString }}" max="{{ $maxDateString }}">
                </div>

                <div style="padding: 0px;" class="col-lg-3 mt-3">
                    <input type="datetime-local" style="height: 49px !important; border-radius: 36px;" id="todate"
                           class="form-control" value="{{ $newEndDate }}" min="{{ $minDateString }}"
                           max="{{ $maxDateString }}">
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
                            <th style="text-align: left;" class="sort">Beer Brand</th>
                            <th style="text-align: center;" class="sort">Ounces</th>
                            <th style="text-align: center;" class="sort">In Temp Range</th>
                            <th style="text-align: center;" class="sort">Temp %</th>
                            <th style="text-align: center;" class="sort">In Range Press</th>
                            <th style="text-align: center;" class="sort">Press %</th>
                        </tr>
                        </thead>
                        <tbody id="tbodyid">
                        </tbody>
                        <tfoot id="footer-total-row">

                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        var fromDate = 'fromdate';
        var toDate = 'todate';

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
                    }
                ]
            });
        });

        $(document).ready(function () {
            window.addEventListener("DOMContentLoaded", () => {
                const tableContainer = document.querySelector(".table-container");

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
                    if (index === 2 || index === 3) {
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

        function changePage(value) {
            var lastPage = $("#lastPage").val();
            var btnDecrement = $(".btn-decrement");
            if (value < 1) {
                $("#currentPage").val(0);
                btnDecrement.prop('cursor', 'not-allowed');
                btnDecrement.prop('disabled', true);
            } else {
                btnDecrement.prop('disabled', false);
                //update current page value

                loadData();
            }

            if (value === 1) {
                btnDecrement.prop('cursor', 'not-allowed');
                btnDecrement.prop('disabled', true);
            } else {
                btnDecrement.prop('cursor', 'pointer');
                btnDecrement.prop('disabled', false);
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
                    loadData();
                },
            });
        }

        $('#ddlFruits').on('change', function () {
            ajaxLocation();
        });

        $('#refreshBtn').on('click', function () {
            $("#currentPage").val(1);
            $("#tbodyid").empty();

            loadData();
        });

        function loadData() {
            var device = $("#mySelect").val()
            var fromdate = $('#fromdate').val();
            var todate = $('#todate').val();
            var location_id = $('#ddlFruits').val();
            console.log(fromdate + "--" + todate);

            $("#footer-total-row").empty();
            $('#spinner-div').show(); //Load button clicked show spinner
            $.ajax({
                url: "{{ url('/in-range-report-data/') }}/" + device + "/" + fromdate + "/" + todate,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    $("#tbodyid").empty();
                    dataList.result.forEach((item => {
                        const row = "<tr>" +
                            "<td style='text-align:left;'>" + item['brand'] + "</td>" +
                            "<td style='text-align:center;'>" + item['total_ounces'] + "</td>" +
                            "<td style='text-align:center;'>" + item['in_range_temp_ounces'] + "</td>" +
                            "<td style='text-align:center;'>" + item['in_range_temp_percent'] + "%</td>" +
                            "<td style='text-align:center;'>" + item['in_range_pressure_ounces'] + "</td>" +
                            "<td style='text-align:center;'>" + item['in_range_pressure_percent'] + "%</td>" +
                            "</tr>";

                        $("#tbodyid").append(row);
                    }));

                    const item = dataList.total;
                    const row = "<tr>" +
                        "<td style='text-align:left; font-weight: 500;'>" + item['brand'] + "</td>" +
                        "<td style='text-align:center; font-weight: 500;'>" + item['total_ounces'] + "</td>" +
                        "<td style='text-align:center; font-weight: 500;'>" + item['in_range_temp_ounces'] + "</td>" +
                        "<td style='text-align:center; font-weight: 500;'>" + item['in_range_temp_percent'] + "%</td>" +
                        "<td style='text-align:center; font-weight: 500;'>" + item['in_range_pressure_ounces'] + "</td>" +
                        "<td style='text-align:center; font-weight: 500;'>" + item['in_range_pressure_percent'] + "%</td>" +
                        "</tr>";

                    $("#footer-total-row").append(row);
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
