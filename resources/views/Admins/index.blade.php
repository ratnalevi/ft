@extends('layouts.master')

@section('title', 'Administration')
@section('content')

    <style>
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
                    <i class="fa fa-users" aria-hidden="true"></i>
                    @php
                        $name = Session::get('userID');
                        $name = DB::table('UserDemographic')
                            ->where('UserID', $name->UserID)
                            ->first();
                    @endphp
                    <span class="ml-3">User Management - {{ $name->FirstName . ' ' . $name->LastName }}</span>
                    @if ($userAdmin)
                        <a class="m-3" href="/add-user" style="color: blue;"><i class="fa fa-plus"
                                                                                aria-hidden="true"></i></a>
                    @endif
                </h5>

            </div>
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <div class="table-responsive">
                <div id="spinner-div" class="pt-5">
                    <div class="mt-5">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="text-align: center">Status</th>
                        <th style="text-align: center">Admin ?</th>
                        <th style="text-align: center"># Locations</th>
                        <th style="text-align: center"># Sub-Accounts</th>
                        @if ($userAdmin)
                            <th style="text-align: center">Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>

                    @if (!empty($users))
                        @foreach ($users->unique('UserID') as $user)
                            <tr>
                                <td>{{ $user->FirstName . ' ' . $user->LastName }}</td>
                                <td>{{ $user->Email }}</td>
                                <td style="text-align: center">

                                    @if ($user->IsActive == '1')
                                        Active
                                    @elseif($user->IsActive == '0')
                                        Suspended
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($user->AdminAccess == '1')
                                        Yes
                                    @elseif($user->AdminAccess == '0')
                                        No
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    0
                                </td>
                                <td style="text-align: center">
                                    0
                                </td>
                                @if ($userAdmin)
                                    <td style="text-align: center">
                                        <a href="{{ url('/edit/account/' . $user->UserID) }}"
                                           style="color: blue; font-size: 20px;padding-right: 6px;">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                                        <a href="#"
                                           onclick="deleteUser('{{ $user->UserID }}', '{{ $user->FirstName . ' ' . $user->LastName }}')"
                                           style="color: blue; font-size: 20px;">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                    @endif

                    @if (!empty($subaccount))

                        @foreach ($subaccount as $user)
                            <tr>
                                {{-- <td>{{ $user->FirstName . ' ' . $user->Middle . ' ' . $user->LastName }}</td> --}}
                                <td>{{ $user->AccountName }}</td>
                                <td>{{ $user->Email }}</td>
                                <td style="text-align: center">

                                    @if ($user->activeAccount == '1')
                                        Active
                                    @elseif($user->activeAccount == '0')
                                        Suspended
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if ($user->AdminAccess == '1')
                                        Yes
                                    @elseif($user->AdminAccess == '0')
                                        No
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    0
                                </td>
                                <td style="text-align: center">
                                    0
                                </td>
                                @if ($userAdmin)
                                    <td style="text-align: center">
                                        <a href="{{ url('/edit/account/' . $user->UserID) }}"
                                           style="color: blue; font-size: 20px;padding-right: 6px;">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                                        <a href="#"
                                           onclick="deleteUser('{{ $user->UserID }}', '{{ $user->AccountName }}')"
                                           style="color: blue; font-size: 20px;">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- <div id="exampleModal" class="modal-dialog modal-dialog-centered">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="ddelete">
                    <h4 style="text-align:center">Delete Account</h4>
                    <p style="text-align:center">Are you sure you want to delete your account?</p>

                    <div class="clearfix">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}


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
                    <h4 style="text-align:center">Delete User</h4>
                    <p style="text-align:center">Are you sure you want to delete User: <span id="accountName"></span>?
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
        function deleteUser(id, name) {
            $('#accountName').text(name);
            $('#exampleModal').modal('show')
            $('#deleteID').text(id);

        }

        $('#deleteAction').on('click', function () {
            var id = $('#deleteID').text();
            $.ajax({
                type: 'get',
                url: '/delete/account/' + id,
                data: {
                    id: id,
                },
                success: function (data) {
                    console.log(data);
                    $('.modal-body').html('');
                    if (data.status == 401) {
                        var table =
                            '<p class="alert alert-warning">You cannot delete your own account!</p>';
                        $('.modal-body').append(table);
                    } else {
                        var table = '<p class="alert alert-success">User Deleted Successfull</p>';
                        $('.modal-body').append(table);
                    }
                    window.location.reload();
                },
            });
        });


        $(document).ready(function () {
            // debugger;
            $('.table').DataTable({
                searching: true,
                paging: true,
                bLengthChange: true,
                info: true,
                ordering: false
            });


        });
    </script>

@endsection
