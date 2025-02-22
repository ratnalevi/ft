@php use App\Models\BeerBrand; @endphp
@php use App\Models\DeviceLine; @endphp
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Port</th>
        <th>Brand</th>
        {{--                        <th>Total Poured (oz)</th>--}}
        {{--                        <th>Estimate keg Vol</th>--}}
        <th>Last Pour</th>
        <th>Last Pour(oz)</th>
        <th>Temp(F)</th>
        <th>PSI</th>
        <th>TDS</th>
        <th>Last Cleaning</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $value)
        {{--        @if($value->DevicesID==$lastdevice->DevicesID)--}}
        <tr>
            <td>---</td>
            <td>
                {{ BeerBrand::where(['BeerBrandsID' => DeviceLine::where(['DevicesID' => $value->DevicesID])->pluck('BeerBrandsID')->first() ])->pluck('Brand')->first() }}
            </td>
            <td>{{round(($value->Pulse * 1.12) / 29.57, 2)}}</td>
            <td>---</td>
            {{--                <td>{{$value->InsertDateTime}}</td>--}}
            {{--                <td>---</td>--}}
            <td>{{$value->Temp}}</td>
            <td>{{$value->Pres}}</td>
            <td>{{$value->TDS}}</td>
            <td>March 01, 2023</td>
        </tr>
        {{--        @endif--}}
    @endforeach
    </tbody>
</table>

{!! $data->render() !!}
