@extends('layouts.master')

@section('title', 'Device Management')
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

        #DataTables_Table_0_length {
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

        #DataTables_Table_0_filter {
            float: left;
            padding-left: 16px;
        }
    </style>
    <style>
        .bg-label-success {
            background-color: #dff7e9 !important;
            color: #28c76f !important;
        }

        .bg-label-warning {
            background-color: #fff1e3 !important;
            color: #ff9f43 !important;
        }

        .bg-label-secondary {
            background-color: #f2f2f3 !important;
            color: #777575 !important;
        }
    </style>
    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span class="ml-3">Device Management </span>
                    <a class="m-3" href="/add-Devimanagement"> <i class="fa fa-plus" aria-hidden="true"></i></a>
                </h5>
            </div>

            {{-- @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                    @php
                        Session::forget('success');
                    @endphp
                </div>
            @endif --}}

            <div class="table-responsive">
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Account</th>
                        <th>Location</th>
                        <th>Device Name</th>
                        <th>Device S/N</th>
                        {{-- <th>Device ID</th> --}}
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($device as $item)
                        <tr>
                            <td>{{ $item->AccountName }}</td>
                            <td>{{ $item->LocationName }}</td>
                            <td>{{ $item->Name }}</td>
                            <td>{{ $item->Serial }}</td>
                            {{-- <td>{{ $item->DevicesID }}</td> --}}
                            <td>
                                @if ($item->RecordStatus == 0)
                                    <a href="#" class="badge bg-label-secondary"
                                        {{-- onclick="changeStatus({{ $item->id }})" --}}>inactive</a>
                                @elseif ($item->RecordStatus == 1)
                                    <a href="#" class="badge bg-label-success"
                                       onclick="changeStatus({{ $item->DevicesID }},2)">active</a>
                                @else
                                    <a href="#" class="badge bg-label-warning"
                                       onclick="changeStatus({{ $item->DevicesID }},1)">disabled</a>
                                @endif
                            </td>
                            <td>
                                <a href="/editDevimanagement/{{ $item->DevicesID }}" style="color: blue;"><i
                                        class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                {{-- <a href="/deleteDevimanagement/{{ $item->DevicesID }}" style="color: blue;"><i
                                            class="fa fa-trash" aria-hidden="true"></i></a> --}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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
                    <h4 style="text-align:center">Change Status</h4>
                    <p style="text-align:center">Are you sure you want to change the status?</p>
                    <label for="" class="hidden" id="DevicesID"></label>
                    <label for="" class="hidden" id="RecordStatus"></label>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="changeAction" class="btn btn-primary">Change</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changeStatus(DevicesID, RecordStatus) {
            $('#exampleModal').modal('show')
            $('#DevicesID').text(DevicesID);
            $('#RecordStatus').text(RecordStatus);
        }

        $('#changeAction').on('click', function () {
            var id = $('#DevicesID').text();
            var RecordStatus = $('#RecordStatus').text();
            $.ajax({
                type: 'get',
                url: '/device/change/' + id,
                data: {
                    RecordStatus: RecordStatus,
                },
                success: function (data) {
                    console.log(data);
                    $('.modal-body').html('');
                    if (data.status == 'success') {
                        var message = '<p class="alert alert-success">' + data.message + '</p>';
                        $('.modal-body').append(message);
                    } else {
                        alert('There is some error....');
                    }
                    window.location.reload();
                },
            });
        });

        $(document).ready(function () {
            // debugger;
            $('.table').DataTable({
                paging: true,
                searching: true,
                bLengthChange: true,
                info: true,
                ordering: true
            });


        });
        $(document).ready(function () {

            window.addEventListener("DOMContentLoaded", () => {
                // const tableContainer = document.querySelector(".table-container");
                // const table = document.querySelector("#myTable");

                // Set table height dynamically
                // tableContainer.style.maxHeight = (window.innerHeight - tableContainer.offsetTop) + "px";

                // Recalculate table height when the window is resized
                // window.addEventListener("resize", () => {
                //     tableContainer.style.maxHeight = (window.innerHeight - tableContainer
                //         .offsetTop) + "px";
                // });
            });

        });
    </script>

@endsection
