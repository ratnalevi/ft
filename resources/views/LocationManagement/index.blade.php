@extends('layouts.master')

@section('title', 'Location Management ')
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
        <div class="card m-3">
            <div class="m-3">
                <h4>
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="ml-2">Administration</span>
                </h4>
                <hr>
                <h5>
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span class="ml-3">Location Management</span>
                    <a class="m-3" href="/add-location"> <i class="fa fa-plus" aria-hidden="true"></i></a>
                </h5>
            </div>
            {{-- F --}}
            <div class="table-responsive">
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Location</th>
                        <th>Contact</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Phone</th>
                        <th>Email</
                        >
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($locations as $location)
                        <tr>
                            <td>{{ $location->LocationName }}</td>
                            <td>{{ $location->LocationDESC }}</td>
                            <td>{{ $location->City }}</td>
                            <td>{{ $location->State }}</td>
                            <td>{{ $location->PhonePrimary }}</td>
                            <td>{{ $location->Email }}</td>

                            <td>
                                <a href="{{ url('/edit/location/' . $location->LocationID) }}" style="color: blue;"><i
                                        class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                <a href="#"
                                   onclick="deleteLocation( '{{ $location->LocationID }}','{{ $location->LocationName }}')"
                                   style="color: blue;"><i class="fa fa-trash" aria-hidden="true"></i></a>
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
                    <h4 style="text-align:center">Delete Location</h4>
                    <p style="text-align:center">Are you sure you want to delete location: <span
                            id="locationName"></span>?
                        This location may be linked with an account and device.</p>
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
        function deleteLocation(id, name) {
            $('#locationName').text(name);
            $('#exampleModal').modal('show')
            $('#deleteID').text(id);

        }

        $('#deleteAction').on('click', function () {
            var id = $('#deleteID').text();
            $.ajax({
                type: 'get',
                url: '/delete/location/' + id,
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
                        var table = '<p class="alert alert-success">Location Deleted Successfully</p>';
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
