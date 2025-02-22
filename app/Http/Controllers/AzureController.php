<?php

namespace App\Http\Controllers;

use App\Models\TestData;
use App\Services\SyncAzureData;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysqli;

class AzureController extends Controller
{
    public function azureform()
    {
        return view('devices.deviceConnection');
    }

    public function azureConnection()
    {
        $hubName = 'DantesFirstIoTHub';
        $sharedAccessKey = 'kfYQbV+J8/O6Gmbb1JixjTK0JOT0PuzCnDcdIOk0HYI=';
        $sharedAccessKeyName = 'iothubowner';
        $apiVersion = '2018-06-30';

        $sasToken = self::generateSasToken($hubName, $sharedAccessKeyName, $sharedAccessKey);
        $url = "https://{$hubName}.azure-devices.net/devices?api-version={$apiVersion}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $sasToken
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Process the response as needed
        // For example, you can check the response code and parse the response body

        return $response;
    }

    public static function generateSasToken($hubName, $sharedAccessKeyName, $sharedAccessKey)
    {
        $expiry = time() + 3600;
        $resourceUri = "{$hubName}.azure-devices.net/devices/{$hubName}";

        $signature = urlencode(base64_encode(hash_hmac('sha256', $resourceUri . "\n" . $expiry, base64_decode($sharedAccessKey), true)));

        return "SharedAccessSignature sr={$resourceUri}&sig={$signature}&se={$expiry}&skn={$sharedAccessKeyName}";
    }

    // azure END Device Connection

    public function addDevice(Request $request)
    {
        $deviceId = $request->device;
        $hubName = 'DantesFirstIoTHub';
        $sharedAccessKey = 'kfYQbV+J8/O6Gmbb1JixjTK0JOT0PuzCnDcdIOk0HYI=';
        $sharedAccessKeyName = 'iothubowner';
        $apiVersion = '2018-06-30'; // Example API version, please verify the correct version for your Azure IoT Hub

        $sasToken = self::generateSasToken($hubName, $sharedAccessKeyName, $sharedAccessKey);
        $url = "https://{$hubName}.azure-devices.net/devices/{$deviceId}?api-version={$apiVersion}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $sasToken,
            'Content-Type: application/json'
        ]);
        $requestBody = [
            'deviceId' => $deviceId,
            'capabilities' => [
                'iotEdge' => true
            ]
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        $response = curl_exec($ch);
        curl_close($ch);

        // Process the response as needed
        // For example, you can check the response code and parse the response body

        return $response;
    }

    //

    public function EventIOThub()
    {
        function handleValidation($req, $res)
        {
            // Check that the request method is POST and content type is "application/json"
            //if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SERVER["CONTENT_TYPE"] === "application/json") {
            // Parse the request body and extract the validation code
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);
            $validationCode = $data[0]['data']['validationCode'];
            // Return the validation code in a 200 OK response
            $responseBody = ['validationResponse' => $validationCode];
            $res->setStatusCode(200);
            $res->setContent(json_encode($responseBody));
            $file = 'requests.log';
            //            file_put_contents($file, $responseBody, FILE_APPEND);
            $res->send();
            //   } else {
            //     // Return a 400 Bad Request response if the request is invalid
            //     $res->setStatusCode(400);
            //     $res->setContent("Very Bad request");
            //     $res->send();
            //   }
        }

        $request = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";

        $bodylog = file_get_contents('php://input');
        $request .= 'Request Body: ' . $bodylog . "\n\n";

        try {
            // Handle incoming requests
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Check if this is a validation request
                $body = file_get_contents('php://input');
                $data = json_decode($body, true);
                $eventType = $data[0]['eventType'];
                if ($eventType === 'Microsoft.EventGrid.SubscriptionValidationEvent') {
                    // Handle validation requests

                    handleValidation(
                        $_SERVER,
                        new class () {
                            public function setStatusCode($statusCode)
                            {
                                http_response_code($statusCode);
                                // header('Content-Type: application/json');
                            }

                            public function setContent($content)
                            {
                                echo $content;
                            }

                            public function send()
                            {
                                // Do nothing
                            }
                        },
                    );
                } else {
                    // Handle event notifications
                    // Parse the event data and perform any necessary actions
                    // ...

                }
            }
        } catch (Exception $e) {
            $error = 'Message: ' . $e->getMessage();
            $file = 'requests.log';
            file_put_contents($file, $request, FILE_APPEND);
        }
    }

    public function EventIOThubAuth()
    {
        return view('EventHUb.AutheventTest');
    }

    // Laravel
    public function AlertCenter1(Request $request)
    {
        $data = '{
            "ID": "435020003",
            "TYPE": "DATA",
            "TIME": "2023-04-03T20:10:05Z",
            "LINE": [
              1,
              2,
              3,
              4,
              5
            ],
            "TEMP": [
              20.07,
              10.00,
              30.00,
              40.00,
              28.00
            ],
            "TEMPMIN": [
              33.99,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "TEMPMAX": [
              34.07,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "PRES": [
              0.00,
              20.00,
              19.00,
              10.00,
              8.00
            ],
            "PRESMIN": [
              0.00,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "PRESMAX": [
              0.00,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "DEPRES": [
              "2022-10-03T21:16:21Z",
              "2023-02-15T14:48:36Z",
              "2022-10-03T21:17:04Z",
              "2022-10-03T21:17:21Z",
              "2022-10-03T21:18:19Z"
            ],
            "REPRES": [
              "2022-10-03T21:16:02Z",
              "2023-02-15T14:48:32Z",
              "2022-10-03T21:16:40Z",
              "2022-10-03T21:16:55Z",
              "2022-10-03T21:17:22Z"
            ],
            "TDS": [
              1.19,
              1.19,
              1.19,
              1.19,
              1.19
            ],
            "TDSMIN": [
              0.99,
              0.99,
              0.99,
              0.99,
              1.19
            ],
            "TDSMAX": [
              1.19,
              1.19,
              1.19,
              1.19,
              1.19
            ],
            "PULSE": [
              0,
              0,
              0,
              0,
              0
            ]
          }';


        $data = json_decode($data, true);


        $servername = "localhost:3302";
        $username = "root";
        $password = "";
        $dbname = "floteq_dev";

        // Create conn
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check conn
        if ($conn->connect_error) {
            die("conn failed: " . $conn->connect_error);
        }


        foreach (array_keys($data['LINE']) as $key => $index) {
            $query = "INSERT INTO LineData (DevicesID, DeviceLinesID, DistAccountID, Unit, Temp, TempMin, TempMax, Pres, PresMin, PresMax, Depress, Repress, TDS, TDSMin, TDSMax, Pulse, ReportDateTime, InsertDateTime, UpdateDateTime)
            VALUES ('" . $data['ID'] . "', '" . $data['LINE'][$key] . "', '3', '1', '" . $data['TEMP'][$key] . "', '" . $data['TEMPMIN'][$key] . "', '" . $data['TEMPMAX'][$key] . "', '" . $data['PRES'][$key] . "', '" . $data['PRESMIN'][$key] . "', '" . $data['PRESMAX'][$key] . "', '" . $data['DEPRES'][$key] . "', '" . $data['REPRES'][$key] . "', '" . $data['TDS'][$key] . "', '" . $data['TDSMIN'][$key] . "', '" . $data['TDSMAX'][$key] . "', '" . $data['PULSE'][$key] . "', '2023-02-10 21:48:56', '2023-02-10 21:48:56', '2023-02-10 21:48:56')";
            mysqli_query($conn, $query);

            $query = "SELECT * FROM DeviceLines INNER JOIN Devices ON DeviceLines.DevicesID = Devices.DevicesID LEFT JOIN Accounts ON Devices.AccountID = Accounts.AccountID WHERE DeviceLines.DevicesID = '" . $data['ID'] . "' AND DeviceLines.Line = '" . $data['LINE'][$index] . "'";
            $result = mysqli_query($conn, $query);
            $deviceLine = mysqli_fetch_object($result);

            print_r($deviceLine);


            $Temp1 = $data['TEMP'][$index] >= $deviceLine->OptTemp + $deviceLine->TempAlertValue;
            $Temp2 = $data['TEMP'][$index] <= $deviceLine->OptTemp - $deviceLine->TempAlertValue;

            $Press1 = $data['PRES'][$index] >= $deviceLine->OptPressure + $deviceLine->PressAlertValue;
            $Press2 = $data['PRES'][$index] <= $deviceLine->OptPressure - $deviceLine->PressAlertValue;


            if ($Temp1) {
                $query1 = "INSERT INTO DeviceLinesAlert (DeviceLinesAlertStatus, AccountID, DevicesID, UserAccountID, AlertID, Line, DeviceStatus, BeerBrandID, KegTypeID, DistributorID, OptTemp, OptPressure, TempPressAlert, TempPressAlertTimeOut, TempAlertValue, PressAlertValue, KegCost, LineLength, AlertDateTime, AckDateTime, RecordStatus, InsertDateTime, UpdateDateTime)
                VALUES ('1', '" . $deviceLine->AccountID . "', '" . $deviceLine->DevicesID . "', '1', '1', '" . $data['LINE'][$index] . "', '1', '" . $deviceLine->BeerBrandsID . "', '" . $deviceLine->KegTypeID . "', '" . $deviceLine->DistAccountID . "', '" . $deviceLine->OptTemp . "', '" . $deviceLine->OptPressure . "', '" . $deviceLine->TempPressAlert . "', '" . $deviceLine->TempPressAlertTimeOut . "', '" . $deviceLine->TempAlertValue . "', '" . $deviceLine->PressAlertValue . "', '" . $deviceLine->KegCost . "', '" . $deviceLine->LineLength . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . $deviceLine->RecordStatus . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "')";
                mysqli_query($conn, $query1);
            } elseif ($Temp2) {
                $query1 = "INSERT INTO DeviceLinesAlert (DeviceLinesAlertStatus, AccountID, DevicesID, UserAccountID, AlertID, Line, DeviceStatus, BeerBrandID, KegTypeID, DistributorID, OptTemp, OptPressure, TempPressAlert, TempPressAlertTimeOut, TempAlertValue, PressAlertValue, KegCost, LineLength, AlertDateTime, AckDateTime, RecordStatus, InsertDateTime, UpdateDateTime)
                VALUES ('1', '" . $deviceLine->AccountID . "', '" . $deviceLine->DevicesID . "', '1', '1', '" . $data['LINE'][$index] . "', '1', '" . $deviceLine->BeerBrandsID . "', '" . $deviceLine->KegTypeID . "', '" . $deviceLine->DistAccountID . "', '" . $deviceLine->OptTemp . "', '" . $deviceLine->OptPressure . "', '" . $deviceLine->TempPressAlert . "', '" . $deviceLine->TempPressAlertTimeOut . "', '" . $deviceLine->TempAlertValue . "', '" . $deviceLine->PressAlertValue . "', '" . $deviceLine->KegCost . "', '" . $deviceLine->LineLength . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . $deviceLine->RecordStatus . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "')";
                mysqli_query($conn, $query1);
            }

            if ($Press1) {
                $query2 = "INSERT INTO DeviceLinesAlert (DeviceLinesAlertStatus, AccountID, DevicesID, UserAccountID, AlertID, Line, DeviceStatus, BeerBrandID, KegTypeID, DistributorID, OptTemp, OptPressure, TempPressAlert, TempPressAlertTimeOut, TempAlertValue, PressAlertValue, KegCost, LineLength, AlertDateTime, AckDateTime, RecordStatus, InsertDateTime, UpdateDateTime)
                VALUES ('1', '" . $deviceLine->AccountID . "', '" . $deviceLine->DevicesID . "', '1', '2', '" . $data['LINE'][$index] . "', '1', '" . $deviceLine->BeerBrandsID . "', '" . $deviceLine->KegTypeID . "', '" . $deviceLine->DistAccountID . "', '" . $deviceLine->OptTemp . "', '" . $deviceLine->OptPressure . "', '" . $deviceLine->TempPressAlert . "', '" . $deviceLine->TempPressAlertTimeOut . "', '" . $deviceLine->TempAlertValue . "', '" . $deviceLine->PressAlertValue . "', '" . $deviceLine->KegCost . "', '" . $deviceLine->LineLength . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . $deviceLine->RecordStatus . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "')";
                mysqli_query($conn, $query2);
            } elseif ($Press2) {
                $query2 = "INSERT INTO DeviceLinesAlert (DeviceLinesAlertStatus, AccountID, DevicesID, UserAccountID, AlertID, Line, DeviceStatus, BeerBrandID, KegTypeID, DistributorID, OptTemp, OptPressure, TempPressAlert, TempPressAlertTimeOut, TempAlertValue, PressAlertValue, KegCost, LineLength, AlertDateTime, AckDateTime, RecordStatus, InsertDateTime, UpdateDateTime)
                VALUES ('1', '" . $deviceLine->AccountID . "', '" . $deviceLine->DevicesID . "', '1', '2', '" . $data['LINE'][$index] . "', '1', '" . $deviceLine->BeerBrandsID . "', '" . $deviceLine->KegTypeID . "', '" . $deviceLine->DistAccountID . "', '" . $deviceLine->OptTemp . "', '" . $deviceLine->OptPressure . "', '" . $deviceLine->TempPressAlert . "', '" . $deviceLine->TempPressAlertTimeOut . "', '" . $deviceLine->TempAlertValue . "', '" . $deviceLine->PressAlertValue . "', '" . $deviceLine->KegCost . "', '" . $deviceLine->LineLength . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . $deviceLine->RecordStatus . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "')";
                mysqli_query($conn, $query2);
            }
        }
    }

    // php
    public function AlertCenter(Request $request)
    {
        $data = '{
            "ID": "435020003",
            "TYPE": "DATA",
            "TIME": "2023-04-03T20:10:05Z",
            "LINE": [
              1,
              2,
              3,
              4,
              5
            ],
            "TEMP": [
              20.07,
              10.00,
              30.00,
              40.00,
              28.00
            ],
            "TEMPMIN": [
              33.99,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "TEMPMAX": [
              34.07,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "PRES": [
              0.00,
              20.00,
              19.00,
              10.00,
              8.00
            ],
            "PRESMIN": [
              0.00,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "PRESMAX": [
              0.00,
              0.00,
              0.00,
              0.00,
              0.00
            ],
            "DEPRES": [
              "2022-10-03T21:16:21Z",
              "2023-02-15T14:48:36Z",
              "2022-10-03T21:17:04Z",
              "2022-10-03T21:17:21Z",
              "2022-10-03T21:18:19Z"
            ],
            "REPRES": [
              "2022-10-03T21:16:02Z",
              "2023-02-15T14:48:32Z",
              "2022-10-03T21:16:40Z",
              "2022-10-03T21:16:55Z",
              "2022-10-03T21:17:22Z"
            ],
            "TDS": [
              1.19,
              1.19,
              1.19,
              1.19,
              1.19
            ],
            "TDSMIN": [
              0.99,
              0.99,
              0.99,
              0.99,
              1.19
            ],
            "TDSMAX": [
              1.19,
              1.19,
              1.19,
              1.19,
              1.19
            ],
            "PULSE": [
              0,
              0,
              0,
              0,
              0
            ]
          }';


        $dataB = json_decode($data, true);

        foreach (array_keys($dataB['LINE']) as $key => $index) {

            DB::table('LineData')->insert([
                'DevicesID' => $dataB['ID'],
                'DeviceLinesID' => $dataB['LINE'][$key],
                'DistAccountID' => '3',
                'Unit' => '1',
                'Temp' => $dataB['TEMP'][$key],
                'TempMin' => $dataB['TEMPMIN'][$key],
                'TempMax' => $dataB['TEMPMAX'][$key],
                'Pres' => $dataB['PRES'][$key],
                'PresMin' => $dataB['PRESMIN'][$key],
                'PresMax' => $dataB['PRESMAX'][$key],
                'Depress' => $dataB['DEPRES'][$key],
                'Repress' => $dataB['REPRES'][$key],
                'TDS' => $dataB['TDS'][$key],
                'TDSMin' => $dataB['TDSMIN'][$key],
                'TDSMax' => $dataB['TDSMAX'][$key],
                'Pulse' => $dataB['PULSE'][$key],
                'ReportDateTime' => date('Y-m-d H:i:s'),
                'InsertDateTime' => date('Y-m-d H:i:s'),
                'UpdateDateTime' => date('Y-m-d H:i:s'),
            ]);


            $deviceLine = DB::table('DeviceLines')
                ->join('Devices', 'DeviceLines.DevicesID', '=', 'Devices.DevicesID')
                ->leftjoin('Accounts', 'Devices.AccountID', '=', 'Accounts.AccountID')
                ->where('DeviceLines.DevicesID', $dataB['ID'])
                ->where('DeviceLines.Line', $dataB['LINE'][$index])
                ->first();


            $Temp1 = $dataB['TEMP'][$index] >= $deviceLine->OptTemp + $deviceLine->TempAlertValue;
            $Temp2 = $dataB['TEMP'][$index] <= $deviceLine->OptTemp - $deviceLine->TempAlertValue;

            $Press1 = $dataB['PRES'][$index] >= $deviceLine->OptPressure + $deviceLine->PressAlertValue;
            $Press2 = $dataB['PRES'][$index] <= $deviceLine->OptPressure - $deviceLine->PressAlertValue;

            if ($Temp1) {

                DB::table('DeviceLinesAlert')->insert([
                    'DeviceLinesAlertStatus' => '1',
                    'AccountID' => $deviceLine->AccountID,
                    'DevicesID' => $deviceLine->DevicesID,
                    'UserAccountID' => '1',
                    'AlertID' => '1',
                    'Line' => $dataB['LINE'][$index],
                    'DeviceStatus' => '1',
                    'BeerBrandID' => $deviceLine->BeerBrandsID,
                    'KegTypeID' => $deviceLine->KegTypeID,
                    'DistributorID' => $deviceLine->DistAccountID,
                    'OptTemp' => $deviceLine->OptTemp,
                    'OptPressure' => $deviceLine->OptPressure,
                    'TempPressAlert' => $deviceLine->TempPressAlert,
                    'TempPressAlertTimeOut' => $deviceLine->TempPressAlertTimeOut,
                    'TempAlertValue' => $deviceLine->TempAlertValue,
                    'PressAlertValue' => $deviceLine->PressAlertValue,
                    'KegCost' => $deviceLine->KegCost,
                    'LineLength' => $deviceLine->LineLength,
                    'AlertDateTime' => date('Y-m-d H:i:s'),
                    'AckDateTime' => date('Y-m-d H:i:s'),
                    'RecordStatus' => $deviceLine->RecordStatus,
                    'InsertDateTime' => date('Y-m-d H:i:s'),
                    'UpdateDateTime' => date('Y-m-d H:i:s')
                ]);

                print_r("Azure Value Temp 1 A" . $dataB['TEMP'][$index] . "||" . $Temp1 . "<br>");
                // print_r("Azure Alert Temp 1 " . "Line" . $dataB['LINE'][$key]  . "temp" . $dataB['TEMP'][$key] . "/" . "DataBase" . $deviceLine->OptTemp + $deviceLine->TempAlertValue . "<br>");
            } elseif ($Temp2) {
                DB::table('DeviceLinesAlert')->insert([
                    'DeviceLinesAlertStatus' => '1',
                    'AccountID' => $deviceLine->AccountID,
                    'DevicesID' => $deviceLine->DevicesID,
                    'UserAccountID' => '1',
                    'AlertID' => '1',
                    'Line' => $dataB['LINE'][$index],
                    'DeviceStatus' => '1',
                    'BeerBrandID' => $deviceLine->BeerBrandsID,
                    'KegTypeID' => $deviceLine->KegTypeID,
                    'DistributorID' => $deviceLine->DistAccountID,
                    'OptTemp' => $deviceLine->OptTemp,
                    'OptPressure' => $deviceLine->OptPressure,
                    'TempPressAlert' => $deviceLine->TempPressAlert,
                    'TempPressAlertTimeOut' => $deviceLine->TempPressAlertTimeOut,
                    'TempAlertValue' => $deviceLine->TempAlertValue,
                    'PressAlertValue' => $deviceLine->PressAlertValue,
                    'KegCost' => $deviceLine->KegCost,
                    'LineLength' => $deviceLine->LineLength,
                    'AlertDateTime' => date('Y-m-d H:i:s'),
                    'AckDateTime' => date('Y-m-d H:i:s'),
                    'RecordStatus' => $deviceLine->RecordStatus,
                    'InsertDateTime' => date('Y-m-d H:i:s'),
                    'UpdateDateTime' => date('Y-m-d H:i:s')
                ]);
                print_r("Azure Value Temp 2 A" . $dataB['TEMP'][$index] . "||" . $Temp2 . "<br>");
            }

            if ($Press1) {
                DB::table('DeviceLinesAlert')->insert([
                    'DeviceLinesAlertStatus' => '1',
                    'AccountID' => $deviceLine->AccountID,
                    'DevicesID' => $deviceLine->DevicesID,
                    'UserAccountID' => '1',
                    'AlertID' => '2',
                    'Line' => $dataB['LINE'][$index],
                    'DeviceStatus' => '1',
                    'BeerBrandID' => $deviceLine->BeerBrandsID,
                    'KegTypeID' => $deviceLine->KegTypeID,
                    'DistributorID' => $deviceLine->DistAccountID,
                    'OptTemp' => $deviceLine->OptTemp,
                    'OptPressure' => $deviceLine->OptPressure,
                    'TempPressAlert' => $deviceLine->TempPressAlert,
                    'TempPressAlertTimeOut' => $deviceLine->TempPressAlertTimeOut,
                    'TempAlertValue' => $deviceLine->TempAlertValue,
                    'PressAlertValue' => $deviceLine->PressAlertValue,
                    'KegCost' => $deviceLine->KegCost,
                    'LineLength' => $deviceLine->LineLength,
                    'AlertDateTime' => date('Y-m-d H:i:s'),
                    'AckDateTime' => date('Y-m-d H:i:s'),
                    'RecordStatus' => $deviceLine->RecordStatus,
                    'InsertDateTime' => date('Y-m-d H:i:s'),
                    'UpdateDateTime' => date('Y-m-d H:i:s')
                ]);
                print_r("Azure Value Pressure 1 A" . $dataB['PRES'][$index] . "||" . $Press1 . "<br>");
                // print_r("Azure Alert Pressure 1 " . "Line" . $dataB['LINE'][$key] . "temp" . $dataB['PRES'][$key] . "/" . "DataBase" . $deviceLine->OptPressure + $deviceLine->PressAlertValue . "<br>");
            } elseif ($Press2) {
                DB::table('DeviceLinesAlert')->insert([
                    'DeviceLinesAlertStatus' => '1',
                    'AccountID' => $deviceLine->AccountID,
                    'DevicesID' => $deviceLine->DevicesID,
                    'UserAccountID' => '1',
                    'AlertID' => '2',
                    'Line' => $dataB['LINE'][$index],
                    'DeviceStatus' => '1',
                    'BeerBrandID' => $deviceLine->BeerBrandsID,
                    'KegTypeID' => $deviceLine->KegTypeID,
                    'DistributorID' => $deviceLine->DistAccountID,
                    'OptTemp' => $deviceLine->OptTemp,
                    'OptPressure' => $deviceLine->OptPressure,
                    'TempPressAlert' => $deviceLine->TempPressAlert,
                    'TempPressAlertTimeOut' => $deviceLine->TempPressAlertTimeOut,
                    'TempAlertValue' => $deviceLine->TempAlertValue,
                    'PressAlertValue' => $deviceLine->PressAlertValue,
                    'KegCost' => $deviceLine->KegCost,
                    'LineLength' => $deviceLine->LineLength,
                    'AlertDateTime' => date('Y-m-d H:i:s'),
                    'AckDateTime' => date('Y-m-d H:i:s'),
                    'RecordStatus' => $deviceLine->RecordStatus,
                    'InsertDateTime' => date('Y-m-d H:i:s'),
                    'UpdateDateTime' => date('Y-m-d H:i:s')
                ]);
                print_r("Azure Value Pressure 1 A" . $dataB['PRES'][$index] . "||" . $Press2 . "<br>");
                // print_r("Azure Alert Pressure 2 " . "Line" . $dataB['LINE'][$key]  . "temp" . $dataB['PRES'][$key] . "/" . "DataBase" . $deviceLine->OptPressure - $deviceLine->PressAlertValue . "<br>");
            }
        }
    }

    public function filters()
    {
        return view('addFilter');
    }

    public function processLineData(): JsonResponse
    {
        $syncObj = new SyncAzureData();
        $data = $syncObj->getRequestDataDecoded();

        Log::channel('telemetry')->info('Message: ======================================');
        Log::channel('telemetry')->info('Message: Data received and processing');
        $syncObj->loadDataIntoDevDb($data);

        return response()->json(['status' => true, 'message' => 'Data processed successfully']);
    }
}
