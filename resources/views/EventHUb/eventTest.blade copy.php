<?php

$request = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";

$bodylog = file_get_contents('php://input');

$request .= 'Request Body: ' . $bodylog . "\n\n";

$file = 'requests.log';
file_put_contents($file, $request, FILE_APPEND);

try {
    // Handle incoming requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if this is a validation request
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        $eventType = $data[0]['eventType'];
        if ($eventType === 'Microsoft.Devices.DeviceTelemetry') {
            // Handle data

            $bodyTag = $data->data->body;

            $decoded_string = base64_decode($bodyTag);
            // this will be the json that you can apply your json on and then insert in database

            foreach ($decoded_string as $index => $item) {
                if (isset($item['LINE'])) {
                    foreach ($item['LINE'] as $key => $line) {
                        DB::table('LineData')->insert([
                            'DevicesID' => '1',
                            'DeviceLinesID' => '3',
                            'DistAccountID' => '3',
                            'Unit' => '1',
                            'Temp' => $item['TEMP'][$key],
                            'TempMin' => $item['TEMPMIN'][$key],
                            'TempMax' => $item['TEMPMAX'][$key],
                            'Pres' => $item['PRES'][$key],
                            'PresMin' => $item['PRESMIN'][$key],
                            'PresMax' => $item['PRESMAX'][$key],
                            'Depress' => $item['DEPRES'][$key],
                            'Repress' => $item['REPRES'][$key],
                            'TDS' => $item['TDS'][$key],
                            'TDSMin' => $item['TDSMIN'][$key],
                            'TDSMax' => $item['TDSMAX'][$key],
                            'Pulse' => $item['PULSE'][$key],
                            'ReportDateTime' => '2023-02-10 21:48:56',
                            'InsertDateTime' => '2023-02-10 21:48:56',
                            'UpdateDateTime' => '2023-02-10 21:48:56',
                        ]);
                    }
                }
            }
        } else {
            // ...
        }
    }
} catch (Exception $e) {
    $error = 'Message: ' . $e->getMessage();

    $file = 'requests.log';
    file_put_contents($file, $request, FILE_APPEND);
}
?>
