<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abnormal Alerts</title>
</head>

<style>
    .styled-table {
        margin: 25px 0;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .styled-table thead tr {
        background-color: #009879;
        color: #ffffff;
        text-align: left;
    }

    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }

    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }

    .styled-table tbody tr:nth-of-type(even) {
        background-color: #f3f3f3;
    }

    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #009879;
    }

    .styled-table tbody tr.active-column {
        font-weight: bold;
        color: #009879;
    }
</style>
<body>

<p>Hi <strong>{{ $location->LocationName }}</strong> Admin,</p>

<p>We have found an abnormal behavior at one of your locations <strong>{{ $location->LocationName }}</strong> during {{ $start }} and {{ $end }}.</p>

<p>Complete details can be found on our portal: <a href="https://devweb01.flotequsa.com/alert-center?location_id={{ $location->LocationID }}}">Alert Center</a>.</p>

@if(count($alerts) > 0)
    @foreach($alerts as $deviceId => $alertsCount)
        @if(count($alertsCount['temperature']) > 0 || count($alertsCount['tds']) > 0
            || count($alertsCount['pressure']) > 0 || count($alertsCount['after_hours']) > 0)
            <p>Device ID: <strong>{{ $deviceId }}</strong></p>

            <table class="styled-table" style="margin: 25px 0;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);">
                <thead>
                <tr>
                    <th>Line ID</th>
                    <th>Beer Brand</th>
                    <th>Temp Alerts</th>
                    <th>Pressure Alerts</th>
                    <th>TDS Alerts</th>
                    <th>After Hour Pours</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lines[$deviceId] as $line)
                    @php
                        $tempOccurrences = $alertsCount['temperature'][$line['Line']]['Occurrences'] ?? 0;
                        $pressOccurrences = $alertsCount['pressure'][$line['Line']]['Occurrences'] ?? 0;
                        $tdsOccurrences = $alertsCount['tds'][$line['Line']]['Occurrences'] ?? 0;
                        $afterOccurrences = $alertsCount['after_hours'][$line['Line']]['Occurrences'] ?? 0;
                    @endphp
                    @if ($tempOccurrences > 0 || $pressOccurrences > 0 || $tdsOccurrences > 0 || $afterOccurrences > 0)
                        <tr>
                            <td>{{ $line['Line'] }}</td>
                            <td>{{ $line['Brand'] }}</td>
                            <td class="{{ $tempOccurrences > 0 ? "active-row" : '' }}">{{ $tempOccurrences }}</td>
                            <td class="{{ $pressOccurrences > 0 ? "active-row" : '' }}">{{ $pressOccurrences ?? 0 }}</td>
                            <td class="{{ $tdsOccurrences > 0 ? "active-row" : '' }}">{{ $tdsOccurrences ?? 0 }}</td>
                            <td class="{{ $afterOccurrences > 0 ? "active-row" : '' }}">{{ $afterOccurrences ?? 0 }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
            <br>
            <br>
        @endif
    @endforeach
@endif
</body>
</html>
