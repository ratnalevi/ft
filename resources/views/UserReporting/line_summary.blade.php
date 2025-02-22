@extends('layouts.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@section('title', 'Home')
@section('content')

    <style>
        .form-control {
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

        i.fa.fa-exclamation-triangle {
            color: red;
        }

        table.dataTable thead .sorting,
        table.dataTable thead .sorting_asc,
        table.dataTable thead .sorting_desc {
            background: none;
        }
    </style>
    {{--    <div class="col-lg-12"> --}}
    {{--        <div class="card"> --}}
    {{--            <div class="row m-3"> --}}
    {{--                <div class="col-lg-9"> --}}
    {{--                    <h4> --}}
    {{--                        <i class="fa fa-bell-o" aria-hidden="true"></i> --}}
    {{--                        <span class="ml-2">Pending Alerts</span> --}}
    {{--                    </h4> --}}
    {{--                    <p class="ml-5"> --}}
    {{--                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> --}}
    {{--                        <span class="ml-3" style="color: red">Newest Alert</span>: Line 1 (coors Light) --}}
    {{--                        Repressurization 02/03/23 11:00 AM --}}
    {{--                    </p> --}}
    {{--                </div> --}}

    {{--                <div class="col-lg-3"> --}}
    {{--                    <p class="mt-3" style="color: blue;text-align:right;"><a href="">Got to alert center</a></p> --}}
    {{--                </div> --}}
    {{--            </div> --}}
    {{--        </div> --}}
    {{--    </div> --}}


    <div class="col-lg-12">

        {{--        <input type="hidden" name="lastDevice" id="lastDevice" value="{{$lastdevice->DevicesID}}"> --}}
        {{--        <input type="hidden" name="currentPage" id="currentPage"> --}}
        {{--        <input type="hidden" name="lastPage" id="lastPage" value="{{$lastPage}}"> --}}

        <div class="card">
            <div class="row m-3">

                <div class="col-lg-8 mt-4">
                    <h4>
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="ml-3"> Line Summary </span>
                    </h4>
                </div>

                {{--                <div class="col-lg-4 mt-4"> --}}
                {{--                    <select class="form-control locationLOca" style="border-radius: 36px;" name="location33" id="ddlFruits"> --}}
                {{--                        @foreach ($locations as $items) --}}
                {{--                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option> --}}
                {{--                            <option value="{{ $items->LocationID }}">{{ $items->LocationName }}</option> --}}
                {{--                        @endforeach --}}
                {{--                    </select> --}}
                {{--                </div> --}}
            </div>
        </div>
    </div>


    <div class="col-lg-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table" id="lineDataTable">
                    <thead style="background: lightgray;">
                    <tr>
                        <th>Line</th>
                        <th>Brand</th>
                        <th>Ounces</th>
                        <th>Pints</th>
                        <th>AvgTemp</th>
                        <th>MaxTemp</th>
                        <th>AvgPres</th>
                        <th>MaxPres</th>
                        <th>AvgTDS</th>
                        <th>LastPourTime</th>
                    </tr>
                    </thead>
                    <tbody id="tbodyid">
                    @foreach ($data as $value)
                        <tr>
                            <td>{{ $value->Line }}</td>
                            <td>{{ $value->Brand }}</td>
                            <td>{{ $value->Ounces }}</td>
                            <td>{{ $value->Pints }}</td>
                            <td>{{ $value->AvgTemp }}</td>
                            <td>{{ $value->MaxTemp }}</td>
                            <td>{{ $value->AvgPres }}</td>
                            <td>{{ $value->MaxPres }}</td>
                            <td>{{ $value->AvgTDS }}</td>
                            <td>{{ $value->LastPourTime }}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
                {{--                <div class="row"> --}}
                {{--                    <div class="col-md-6" style="text-align: left;padding-left: 1%;"> --}}
                {{--                        --}}{{--                        <span id="info" style="margin-left: 1%;">1 to 10 of {{$total}}</span> --}}
                {{--                    </div> --}}
                {{--                    <div class="col-md-6" style="text-align: right;"> --}}
                {{--                        <button class="btn btn-primary btn-sm btn-decrement" onclick="changePage(parseInt($('#currentPage').val())-1);"> << Previous </button> --}}
                {{--                        <button class="btn btn-primary btn-sm btn-increment" style="margin-right: 2%;" onclick="changePage(parseInt($('#currentPage').val())+1);">Next >> </button> --}}
                {{--                    </div> --}}
                {{--                </div> --}}
            </div>
        </div>
    </div>

    <script>
        {{--        // A $( document ).ready() block. --}}
        {{--        $( document ).ready(function() { --}}
        {{--            $("#currentPage").val(1); --}}
        {{--            $(".btn-decrement").prop('disabled', true); --}}
        {{--        }); --}}

        {{--        function changePage(value){ --}}
        {{--            var lastPage = $("#lastPage").val(); --}}
        {{--            if(value < 1 ){ --}}
        {{--                $("#currentPage").val(0); --}}
        {{--                $(".btn-decrement").prop('cursor', 'not-allowed'); --}}
        {{--                $(".btn-decrement").prop('disabled', true); --}}
        {{--            }else{ --}}
        {{--                $(".btn-decrement").prop('disabled', false); --}}
        {{--                //update current page value --}}
        {{--                $("#currentPage").val(value); --}}
        {{--                var newPage     = value; --}}
        {{--                var device      = $("#mySelect").val(); --}}
        {{--                var daysfilter  = $("#daysfilter").val(); --}}
        {{--                loadData(device,daysfilter,newPage); --}}

        {{--            } --}}

        {{--            if(value==1){ --}}
        {{--                $(".btn-decrement").prop('cursor', 'not-allowed'); --}}
        {{--                $(".btn-decrement").prop('disabled', true); --}}
        {{--            }else{ --}}
        {{--                $(".btn-decrement").prop('cursor', 'pointer'); --}}
        {{--                $(".btn-decrement").prop('disabled', false); --}}
        {{--            } --}}
        {{--            if(value==lastPage){ --}}
        {{--                $(".btn-increment").prop('cursor', 'not-allowed'); --}}
        {{--                $(".btn-increment").prop('disabled', true); --}}
        {{--            }else{ --}}
        {{--                $(".btn-increment").prop('cursor', 'pointer'); --}}
        {{--                $(".btn-increment").prop('disabled', false); --}}
        {{--            } --}}

        {{--        } --}}

        $('#ddlFruits').on('change', function () {
            // $("#currentPage").val(1);
            // var page = $("#currentPage").val();
            var location = $("#ddlFruits").val();
            // loadData(location,page);
            loadData(location, 1);
        });

        function loadData(location, newPage) {
            $.ajax({
                url: "{{ url('/load/line/summery/date') }}/" + location + "/" + newPage,
                type: 'GET',
                dataType: 'json',
                success: function (dataList) {
                    // $("#currentPage").val(newPage);

                    $("#tbodyid").empty();

                    // $('.table').DataTable().clear().destroy();

                    for (var i = 0; i < dataList.result.length; i++) {

                        var row = "<tr>" +
                            "<td>---</td>" +
                            "<td>" + dataList.result[i]['Temp'] + "</td>" +
                            "<td>" + dataList.result[i]['Temp'] + "</td>" +
                            // "<td>---</td>" +
                            "<td>" + dataList.result[i]['Temp'] + "</td>" +
                            "<td>" + dataList.result[i]['Pres'] + "</td>" +
                            "<td>" + dataList.result[i]['TDS'] + "</td>" +
                            "<td>" + dataList.result[i]['TDS'] + "</td>" +
                            "</tr>";
                        $(".table tbody").append(row);
                    }
                    // $('#info').html('');
                    // var info = '<p style="margin-left: 1%;">'+dataList.from+' to '+dataList.to+' off '+dataList.total+'</p>';
                    // $("#info").html(info);
                    // $("#lastPage").val(dataList.lastPage);
                }
            });
        }
    </script>

@endsection
