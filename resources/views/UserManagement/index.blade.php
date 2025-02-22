@extends('layouts.master')

@section('title', 'Line Management')
@section('content')

    <style>
        .fa,
        .fas {
            font-weight: 900;
            font-size: 22px;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border-top: 1px solid #e3e8f7;
            border-bottom-right-radius: 0.3rem;
            border-bottom-left-radius: 0.3rem;
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

        div#DataTables_Table_0_length {
            float: right;
            padding-right: 16px;
        }

        .table-responsive {
            display: block;
            width: 98%;
            padding: 9px;
            overflow-x: scroll;
            -webkit-overflow-scrolling: touch;
        }

        div#DataTables_Table_0_filter {
            float: left;
            padding-left: 16px;
        }
    </style>


    @php
        $name = Session::get('userID');
        $user = DB::table('UserAccount')
            ->where('UserID', $name->UserID)
            ->select('AdminAccess')
            ->where('AdminAccess', '0')
            ->first();
        $userAdmin = DB::table('UserAccount')
            ->where('UserID', $name->UserID)
            ->select('AdminAccess')
            ->where('AdminAccess', '1')
            ->first();

        $name = DB::table('UserDemographic')
            ->where('UserID', $name->UserID)
            ->first();
    @endphp

    <div class="col-lg-12">
        <div class="card m-3">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-sliders" aria-hidden="true"></i>
                    <span class="ml-3">Line Management</span>


                    @if ($userAdmin)
                        <input type="text" hidden value="{{ $userAdmin->AdminAccess }}" class="adminM" id="adminM">
                    @elseif($user)
                        <input type="text" hidden value="{{ $user->AdminAccess }}" class="usersM" id="usersM">
                    @endif

                    @if ($userAdmin)
                        <a class="m-3" href="#" onclick="addLine()" id="addLinemanagementLink"> <i
                                class="fa fa-plus" aria-hidden="true"></i></a>
                    @endif
                </h5>
            </div>


            <div class="m-3">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <label for="location" class="ml-2">Location</label>
                        <select style="border-radius: 36px;" class="form-control" name="" id="ddlFruits">
                            {{-- @foreach ($locationsUsers as $items)
                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                            @endforeach --}}
                            @foreach ($locationsUsers as $items)
                                @if (session()->has('selected_location'))
                                    @if (session('selected_location') == $items->LocationID)
                                        <p>Selected Location: {{ session('selected_location') }}</p>
                                        <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}
                                        </option>
                                    @else
                                        <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                                    @endif
                                @else
                                    @if ($items->LocationID == $name->LocationID)
                                        <option value="{{ $items->LocationID }}" selected>{{ $items->LocationName }}
                                        </option>
                                    @else
                                        <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="devices" class="ml-2">Devices</label>
                        <select class="form-control devices" style="border-radius: 36px;" name="devices" id="mySelect">
                            @foreach ($devices as $items)
                                @if (session()->has('selected_device') && session('selected_device') == $items->DevicesID)
                                    <option selected value="{{ $items->DevicesID }}">{{ $items->Name }}</option>
                                @else
                                    <option value="{{ $items->DevicesID }}">{{ $items->Name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 mt-4 d-flex justify-content-center align-items-center">
                        <a id="refreshBtn" href="#">
                            <i class="fa fa-refresh" aria-hidden="true" style="font-size: 100%;"> Refresh</i>
                        </a>
                    </div>
                </div>
            </div>


            <div class="table-responsive mt-2">
                <div class="table-responsive">
                    <table class="table p-5 ml-2 mr-2">
                        <thead>
                        <tr>
                            <th class="sort">Line</th>
                            <th class="sort">Brand</th>
                            <th class="sort" style="text-align: center">Keg Type</th>
                            <th class="sort" style="text-align: center">Cost</th>
                            <th class="sort" style="text-align: center">temp</th>
                            {{-- <th class="sort" style="text-align: center">Pressure</th> --}}
                            <th class="sort" style="text-align: center;">Line Length</th>
                            <th class="sort">Distributor</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="DataList">
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="spinner-div" class="pt-5">
                <div class="mt-5">
                    <div class="spinner-border text-primary" role="status">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 style="text-align:center">Delete Line</h4>
                    <p style="text-align:center">Are you sure you want to delete this line?</p>
                    <label for="" class="hidden" id="deleteID"></label>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="deleteAction" class="btn btn-primary">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function addLine() {
            var locationDropdown = document.getElementById('ddlFruits');
            var deviceDropdown = document.getElementById('mySelect');

            var selectedLocation = locationDropdown.value;
            var selectedDevice = deviceDropdown.value;
            if (selectedLocation && selectedDevice) {
                var url = '/add-linemanagement/' + selectedLocation + '/' + selectedDevice;
                window.location.href = url; // Redirect to the composed URL
            } else {
                alert('Please select both location and device.');
            }
        }

        $(document).ready(function () {

            // window.addEventListener("DOMContentLoaded", () => {
            //     const tableContainer = document.querySelector(".table-container");
            //     const table = document.querySelector("#myTable");

            //     // Set table height dynamically
            //     tableContainer.style.maxHeight = (window.innerHeight - tableContainer.offsetTop) + "px";

            //     // Recalculate table height when the window is resized
            //     window.addEventListener("resize", () => {
            //         tableContainer.style.maxHeight = (window.innerHeight - tableContainer
            //             .offsetTop) + "px";
            //     });
            // });

            // Start Sorting data
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
                    var valA = getCellValue(a, index);
                    var valB = getCellValue(b, index);
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
                };
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }

            // END Sorting data

            realoadAjaxFirst()

            $('#refreshBtn').on('click', function () {
                var ddlFruits = $('#ddlFruits').val();
                var mySelect = $('#mySelect').val();
                $('.table').DataTable().destroy();
                $('#spinner-div').show();
                ajaxCall(ddlFruits, mySelect);
            });


            $('#ddlFruits').on('change', function () {
                realoadAjax();
            });

            function realoadAjax() {
                var location_id = $('#ddlFruits').val();
                $('#mySelect').html("")
                $.ajax({
                    url: "{{ url('/load/devices') }}/" + location_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (dataList) {
                        $('#mySelect').empty();
                        var mySelect = $('#mySelect');
                        console.log(dataList);
                        var option = '';
                        dataList.forEach(element => {
                            option += '<option value=' + element.DevicesID + '>' + element
                                    .Name +
                                '</option>';
                        });

                        $('#mySelect').append(option);
                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong while loading devices against location');
                    }
                });
            }

            function realoadAjaxFirst() {
                var location_id = $('#ddlFruits').val();

                $('#mySelect').html("")
                $.ajax({
                    url: "{{ url('/load/devices') }}/" + location_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (dataList) {
                        $('#mySelect').empty();
                        var mySelect = $('#mySelect');
                        var option = '';
                        var selectedDevice = '{{ session('selected_device') }}';

                        dataList.forEach(element => {
                            if (selectedDevice && selectedDevice == element.DevicesID) {
                                option += '<option selected value=' + element.DevicesID + '>';
                            } else {
                                option += '<option value=' + element.DevicesID + '>';
                            }
                            option += element.Name;
                            option += '</option>';
                        });

                        $('#mySelect').append(option);
                    },
                    complete: function () {
                        var ddlFruits = $('#ddlFruits').val();
                        var mySelect = $('#mySelect').val();
                        ajaxCall(ddlFruits, mySelect);
                    },
                    error: function (textStatus, errorThrown) {
                        alert('something went wrong while loading devices against location');
                    }
                });
            }

            function ajaxCall(ddlFruits, mySelect) {
                $('#DataList').html('');
                $.ajax({
                    url: "/Line-management/" + ddlFruits + "/" + mySelect,
                    type: 'GET',
                    success: function (res) {
                        console.log(res);
                        var option = '';
                        res.forEach(element => {

                            amount = element.KegCost;
                            const formattedAmount = amount.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                            console.log(formattedAmount);

                            var im = $('.adminM').val();
                            var i2 = $('#adminM').val();
                            option += '<tr>';
                            option += '<td>' + element.Line + '</td>';
                            option += '<td>' + element.Brand + '</td>';

                            option += '<td style="text-align: center;">' + element.KeyName +
                                '</td>';

                            option += '<td style="text-align: center;">' +
                                '$' + formattedAmount + '</td>';
                            option += '<td style="text-align: center;">' + element.OptTemp +
                                '</td>';
                            // option += '<td style="text-align: center;">' + element.Pressure +
                            //     " psi" +
                            //     '</td>';
                            option += '<td style="text-align: center;">' + element.LineLength +
                                " ft" + '</td>';
                            option += '<td>' + element.DistName + '</td>';
                            if ($('.adminM').val() == '1' || $('.adminM').val()) {
                                option +=
                                    '<td><a style="color: blue;" href="/edit-linemanagement/' +
                                    element.DeviceLinesID +
                                    '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                option += '<a style="color: blue;" onclick="deleteLine(' +
                                    element.DeviceLinesID +
                                    ')" href="#"> <i class="fa fa-trash" aria-hidden="true"></i></a></td>';
                            } else {
                                option += '<td>---</td>';
                            }
                            option += '</tr>';
                        });
                        $('#DataList').append(option);
                        $('.table').DataTable({
                            searching: true,
                            paging: true,
                            bLengthChange: true,
                            info: true,
                            ordering: true
                        });
                    },
                    complete: function () {
                        $('#spinner-div').hide(); //Request is complete so hide spinner
                    }
                });
            }

        });

        function deleteLine(DeviceLinesID) {
            $('#exampleModal').modal('show')
            $('#deleteID').text(DeviceLinesID);

        }

        $('#deleteAction').on('click', function () {
            var id = $('#deleteID').text();
            $.ajax({
                type: 'get',
                url: '/delete/line/' + id,
                data: {
                    id: id,
                },
                success: function (data) {
                    console.log(data);

                    $('.modal-body').html('');
                    if (data.status == 404) {
                        var table = '<p class="alert alert-warning">' + data.message + '</p>';
                        $('.modal-body').append(table);
                    } else {
                        var table = '<p class="alert alert-success">Line Deleted Successfull</p>';
                        $('.modal-body').append(table);
                    }
                    window.location.reload();
                },
            });
        });
    </script>

@endsection
