@extends('layouts.master')

@section('title', 'Brands Management')
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
                    <i class="fa fa-codiepie" aria-hidden="true"></i>
                    <span class="ml-3">Brand Management</span>
                    <a class="m-3" href="/add-brand"> <i class="fa fa-plus" aria-hidden="true"></i></a>
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table p-5 ml-2 mr-2">
                    <thead>
                    <tr>
                        <th>Brand</th>
                        <th>ABV</th>
                        <th>Brewer</th>
                        <th>Beer Type</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($sortedBrands as $brand)
                        <tr>
                            <td>{{ $brand->Brand }}</td>
                            <td>{{ $brand->ABV }}</td>
                            <td>{{ $brand->Comments }}</td>
                            <td>{{ $brand->beertypes->Description }}</td>
                            <td>
                                <a href="/editbrand/{{ $brand->BeerBrandsID }}">
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
