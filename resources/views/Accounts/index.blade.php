@extends('layouts.master')

@section('title', 'Administration')
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
    @endphp

    <div class="col-lg-12">
        <div class="card">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-address-card" aria-hidden="true"></i>
                    @php
                        $name = Session::get('userID');
                        $name = DB::table('UserDemographic')
                            ->where('UserID', $name->UserID)
                            ->first();
                    @endphp
                    <span class="ml-3">Accounts Management - {{ $name->FirstName . ' ' . $name->LastName }}</span>
                    <a class="m-3" href="/add-account"> <i class="fa fa-plus" aria-hidden="true"></i></a>
                </h5>

            </div>
            {{-- @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif --}}
            <div id="spinner-div" class="pt-5">
                <div class="mt-5">
                    <div class="spinner-border text-primary" role="status">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $sr = 0;
                    @endphp
                    @foreach ($accounts as $account)
                        <tr>
                            <td>{{ $account->AccountName }}</td>
                            @if ($account->IsActive == 1)
                                <td>Active</td>
                            @else
                                <td>disabled</td>
                            @endif
                            <td>{{ $account->EmailTechnical }}</td>
                            <td>{{ $account->City }}</td>
                            <td>{{ $account->State }}</td>
                            <td>
                                <a href="{{ url('/edit-account/' . $account->AccountID) }}" style="color: blue;"><i
                                        class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href="#"
                                   onclick="deleteAccount( '{{ $account->AccountID }}','{{ $account->AccountName }}')"
                                   style="color: blue;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal -->
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
                    <h4 style="text-align:center">Delete Account</h4>
                    <p style="text-align:center">Are you sure you want to delete account: <span id="accountName"></span>?
                    </p>
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
        function deleteAccount(id, name) {
            $('#accountName').text(name);
            $('#exampleModal').modal('show')
            $('#deleteID').text(id);

        }

        $('#deleteAction').on('click', function () {
            var id = $('#deleteID').text();
            $.ajax({
                type: 'get',
                url: '/delete-account/' + id,
                data: {
                    id: id,
                },
                success: function (data) {
                    console.log(data);
                    $('.modal-body').html('');
                    if (data.status == 401) {
                        var table =
                            '<p class="alert alert-warning">' + data.message + '</p>';
                        $('.modal-body').append(table);
                    } else {
                        var table = '<p class="alert alert-success">Account Deleted Successfully</p>';
                        $('.modal-body').append(table);
                    }
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                },
            });
        });
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
