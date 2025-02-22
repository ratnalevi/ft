@extends('layouts.master')

@section('title', 'Alert Center')
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

    <div class="col-lg-12">
        <h5 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Administration/</span>
            <u>Alert Management</u>
        </h5>
        <div class="card">
            <div
                class="mt-0 card-header sticky-element d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
                <h4 class="mb-0"><i class="fa fa-bell" aria-hidden="true"></i>
                    <span class="ml-2">Alert Management</span>
                </h4>
                <div class="action-btns">
                    <a href="/add-alert" class="btn btn-primary waves-effect waves-light">+ Add Record</a>
                </div>
            </div>
            <hr class="mb-4 mt-0">
            <div class="table-responsive">
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Alert ID</th>
                        <th>Alert Name</th>
                        <th>Alert Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($alerts as $alert)
                        <tr>
                            <td>{{ $alert->AlertID }}</td>
                            <td>{{ $alert->AlertName }}</td>
                            <td>{{ $alert->AlertDescription }}</td>
                            <td>
                                <a href="/editalert/{{ $alert->AlertID }}">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.table').DataTable({
                searching: true,
                paging: true,
                bLengthChange: true,
                info: true,
                ordering: true
            });
        });
    </script>

@endsection
