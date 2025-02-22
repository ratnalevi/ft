<?php

namespace App\Services;

use App\Models\Devices;
use App\Models\Location;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use PDO;

class SyncAzureData
{
    public const TDS_UPPER_LIMIT = 650;
    public const DATE_FORMAT = 'Y-m-d';
    public const TIME_FORMAT = 'H:i:s';
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function closeDatabaseConnections($devDb, $prodDb = null): void
    {
        if ($devDb) {
            $devDb->close();
        }

        if ($prodDb) {
            mysqli_close($prodDb);
        }
    }

    public function getRequestDataDecoded()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Log::channel('telemetry')->error('Message: Invalid request method.' . $_SERVER['REQUEST_METHOD']);
            die();
        }

        $request = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";

        // Check if this is a validation request
        $body = file_get_contents('php://input');

        $request .= 'Request Body: ' . $body . "\n\n";
        Log::channel('telemetry')->info('Message: ' . $request);

        $data = json_decode($body, true);
        if (empty($data)) {
            Log::channel('telemetry')->error('Message: Empty data received.');
            die();
        }

        $eventType = $data[0]['eventType'];
        if ($eventType !== 'Microsoft.Devices.DeviceTelemetry') {
            Log::channel('telemetry')->error('Message: ' . 'Invalid event type: ' . $eventType);
            die();
        }

        $bodyTag = $data[0]['data']['body'];
        $decoded_string = base64_decode($bodyTag) . "\n\n";

        // this will be the json that you can apply your json on and then insert in database
        $dataBase = base64_decode($bodyTag);
        if (empty($dataBase)) {
            Log::channel('telemetry')->error('Message: ' . 'Invalid data received: ' . $bodyTag);
            die();
        }

        return json_decode($dataBase, true);
    }

    /**
     * @throws Exception
     */
    public function getOneMessageAndInsert($requestMethod): bool
    {
        $data = $this->peekMessages($requestMethod);
        if (empty($data)) {
            Log::channel('telemetry')->error('Message: ' . "Empty data received");
            return true;
        }

        $eventType = $data['eventType'] ?? '';
        Log::channel('telemetry')->info('Message: ' . "Empty type" . $eventType);
        Log::channel('telemetry')->error('Message: ' . "Event Type: " . $eventType);

        $bodyTag = $data['data']['body'] ?? '';
        if (empty($bodyTag)) {
            Log::channel('telemetry')->error('Message: ' . "Empty body data received");
            return true;
        }

        $decoded_string = base64_decode($bodyTag) . "\n\n";
        if (empty($decoded_string)) {
            Log::channel('telemetry')->error('Message: ' . "Empty body data received");
            return true;
        }

        $eventData = json_decode(base64_decode($bodyTag), true);
        if (empty($eventData)) {
            Log::channel('telemetry')->error('Message: ' . "Empty body data received");
            return true;
        }

        $deviceId = $eventData['ID'];

        $device = Devices::find($deviceId);
        if (empty($device)) {
            Log::channel('telemetry')->error('Message: ' . 'Device not found in our database');
            return true;
        }

        $location = Location::find($device->LocationID);
        if (empty($location)) {
            Log::channel('telemetry')->error('Message: ' . 'Device not mapped to any Location in our database');
            return true;
        }


        if ($eventType != 'Microsoft.Devices.DeviceTelemetry') {
            Log::channel('telemetry')->error('Message: ' . "We dont process this event");
            return true;
        }

        $dateRanges = [
            [
                "from" => "2024-06-20 12:00:00",
                "to" => "2024-06-22 00:59:59"
            ],
            [
                "from" => "2024-06-16 05:00:00",
                "to" => "2024-06-16 06:59:59"
            ],
            [
                "from" => "2024-06-13 20:00:00",
                "to" => "2024-06-14 08:59:59"
            ],
            [
                "from" => "2024-06-12 01:00:00",
                "to" => "2024-06-12 14:59:59"
            ]
        ];

        $eventTime = $eventData['TIME'];
        $dateObj = new DateTime($eventTime);
        $dateObj->setTimezone(new DateTimeZone($location->TimeZone));
        $reportDateTime = $dateObj->format(self::DATE_TIME_FORMAT);

        Log::channel('telemetry')->info('Report Date Time for this event: ' . $reportDateTime);
        $inRange = false;
        $rangeDates = [];
        foreach ($dateRanges as $dateRange) {
            if ($reportDateTime >= $dateRange['from'] && $reportDateTime <= $dateRange['to']) {
                $inRange = true;
                $rangeDates = $dateRange;
                break;
            }
        }

        if (!$inRange) {
            Log::channel('telemetry')->error('Message: ' . "We dont process this event as it is not in given time ranges");
            return true;
        }

        Log::channel('telemetry')->info("Date $reportDateTime is in between " . $rangeDates['from'] . " and " . $rangeDates['to']);
        //        $checkDeviceId = 435020109;
        //        if ($deviceId != $checkDeviceId) {
        //            Log::channel('telemetry')->info('Message: ' . "Ignoring events for this device");
        //            return true;
        //        }

        Log::channel('telemetry')->info('Message: ' . "Processing event for Device ID: " . $deviceId);
        $this->loadDataIntoDevDb($eventData);

        return true;
    }

    /**
     * Function to peek messages from the queue
     *
     * @param $requestMethod
     * @return false|mixed
     *
     */
    public function peekMessages($requestMethod): mixed
    {
        $namespace = 'busservice1';
        $queueName = 'queue1';
        $keyName = 'RootManageAccessKey';
        $key = 'FvOVisecKFTZOXK8v2hij/BdaaUVIDbfJ+ASbMupVVU=';
        $apiVersion = '2017-04';

        $url = "https://$namespace.servicebus.windows.net/$queueName/messages/head?timeout=60&api-version=$apiVersion";

        $headers = [
            'Authorization: ' . $this->generateSasToken($namespace, $keyName, $key),
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestMethod);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
            return false;
        } else {
            $data = json_decode($response, true);
            curl_close($ch);

            if (empty($data)) {
                echo "No more messages.\n";
                return false;
            }

            return $data;
        }
    }

    public function generateSasToken($uri, $sasKeyName, $sasKeyValue): string
    {
        $targetUri = strtolower(rawurlencode(strtolower($uri)));
        $expires = time();
        $expires = $expires + 3600;
        $toSign = $targetUri . "\n" . $expires;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $sasKeyValue, true)));

        return "SharedAccessSignature sr=" . $targetUri . "&sig=" . $signature . "&se=" . $expires . "&skn=" . $sasKeyName;
    }

    public function loadDataIntoDevDb($data): void
    {
        try {
            $dbConn = $this->getDevDbConnection();
            Log::channel('telemetry')->info('Message: Lines received ' . $data['ID'] . ' are ' . implode(',', $data['LINE']));
            foreach ($data['LINE'] as $index => $value) {
                $this->processTelemetryData($dbConn, $data, $index);
            }

            $dbConn->close();
            Log::channel('telemetry')->info('Message: Database connection closed successfully');
            Log::channel('telemetry')->info('Message: ======================================');
        } catch (Exception $e) {
            Log::channel('telemetry')->error('Message: ' . $e->getMessage());
            Log::channel('telemetry')->error($e->getTraceAsString());
        }
    }

    public function getDevDbConnection()
    {
        $defaultConnection = config('database.default');
        $defaultConnection = config('database.connections.' . $defaultConnection);

        $dbname = $defaultConnection['database'];
        $servername = $defaultConnection['host'];
        $username = $defaultConnection['username'];
        $password = $defaultConnection['password'];
        $cert = $defaultConnection['options'][PDO::MYSQL_ATTR_SSL_CA];

        $devDb = mysqli_init();
        mysqli_ssl_set($devDb, null, null, $cert, null, null);
        mysqli_real_connect($devDb, $servername, $username, $password, $dbname, 3306, MYSQLI_CLIENT_SSL);
        if (mysqli_connect_errno()) {
            echo('Failed to connect to MySQL: ' . mysqli_connect_error());
            exit();
        }

        return $devDb;
    }

    /**
     * @throws Exception
     * @throws Exception
     */
    public function processTelemetryData($dbConn, $data, $index): void
    {
        if (empty($data)) {
            return;
        }

        $deviceId = $data['ID'];
        $device = Devices::find($deviceId);
        if (empty($device)) {
            Log::channel('telemetry')->error('Message: ' . 'Device not found in our database');
            return;
        }

        $location = Location::find($device->LocationID);
        if (empty($location)) {
            Log::channel('telemetry')->error('Message: ' . 'Device not mapped to any Location in our database');
            return;
        }

        Config::set('app.timezone', $location->TimeZone);

        $depressValue = $data['DEPRES'][$index];
        $repressValue = $data['REPRES'][$index];
        $line = $data['LINE'][$index];
        $temperature = $data['TEMP'][$index];
        $minTemperature = $data['TEMPMIN'][$index];
        $maxTemperature = $data['TEMPMAX'][$index];
        $pressure = $data['PRES'][$index];
        $minPressure = $data['PRESMIN'][$index];
        $maxPressure = $data['PRESMAX'][$index];
        $tds = $data['TDS'][$index];
        $minTds = $data['TDSMIN'][$index];
        $maxTds = $data['TDSMAX'][$index];
        $pulse = $data['PULSE'][$index];
        $time = $data['TIME'];

        $dateObj = new DateTime($time);
        $dateObj->setTimezone(new DateTimeZone($location->TimeZone));
        $reportDateTime = $dateObj->format(self::DATE_TIME_FORMAT);

        $depressTime = new DateTime($depressValue);
        $depressTime->setTimezone(new DateTimeZone($location->TimeZone));
        $depressTimeFormatted = $depressTime->format(self::DATE_TIME_FORMAT);

        $repressTime = new DateTime($repressValue);
        $repressTime->setTimezone(new DateTimeZone($location->TimeZone));
        $repressTimeFormatted = $repressTime->format(self::DATE_TIME_FORMAT);

        Log::channel('telemetry')->info('Message: ' . 'Processing Device ID: ' . $deviceId . ' and Line: ' . $line . " Report Date Time: " . $reportDateTime);

        $query = "INSERT INTO AzureEventData (DeviceID, ReportDateTime, EventType, EventData) VALUES($deviceId, '" . $reportDateTime . "', 'Microsoft.Devices.DeviceTelemetry', '" . json_encode($data) . "');";
        $dbConn = $this->getDevDbConnection();
        mysqli_query($dbConn, $query);

        if ($temperature == 0 || $pressure == 0) {
            Log::channel('telemetry')->error('Message: ' . 'Temp and pressure is 0 for ' . $reportDateTime . '. So skipping this record');
            return;
        }
// insert into LineData_DeviceId, index, AI from 1
            $newTableName = "LineData_" . $deviceId;

            // Check if the table exists
            // $tableExists = DB::select("SHOW TABLES LIKE ?", [$newTableName]);
            // if (!$tableExists) {
            //     // Clone table structure including indexes
            //     DB::statement("CREATE TABLE $newTableName LIKE LineData");
            //     // Reset AUTO_INCREMENT to start from 1
            //     DB::statement("ALTER TABLE $newTableName AUTO_INCREMENT = 1");
            // }

            // Step 1: Check if the table exists
            $checkTableQuery = "SHOW TABLES LIKE '$newTableName'";
            $tableExists = mysqli_query($dbConn, $checkTableQuery);

            if (mysqli_num_rows($tableExists) == 0) {
                // Step 2: Create the table if it doesn't exist (clone structure from LineData)
                $createTableQuery = "CREATE TABLE $newTableName LIKE LineData";
                mysqli_query($dbConn, $createTableQuery);

                // Step 3: Reset AUTO_INCREMENT to 1
                $resetAutoIncrementQuery = "ALTER TABLE $newTableName AUTO_INCREMENT = 1";
                mysqli_query($dbConn, $resetAutoIncrementQuery);
            }

            // Insert data into the newly created table
            // DB::table($newTableName)->insert($data);
            $sql22 = "INSERT INTO $newTableName (DevicesID, ReportDateTime, DeviceLinesID, DistAccountID, Unit, Temp, TempMin, TempMax, Pres, PresMin, PresMax, Depress, Repress, TDS, TDSMin, TDSMax, Pulse)
                        VALUES ('$deviceId', '$reportDateTime', '$line', '4', '1', '$temperature', '$minTemperature', '$maxTemperature', '$pressure', '$minPressure', '$maxPressure', '$depressTimeFormatted', '$repressTimeFormatted', '$tds', '$minTds', '$maxTds', '$pulse')";
            mysqli_query($dbConn, query: $sql22);
        // $sql22 = "INSERT INTO LineData (DevicesID,ReportDateTime,DeviceLinesID,DistAccountID,Unit ,Temp, TempMin, TempMax, Pres, PresMin, PresMax, Depress, Repress, TDS, TDSMin, TDSMax, Pulse)
        //             VALUES ('$deviceId','$reportDateTime','$line','4','1', '$temperature', '$minTemperature', '$maxTemperature', '$pressure', '$minPressure', '$maxPressure', '$depressTimeFormatted', '$repressTimeFormatted', '$tds', '$minTds', '$maxTds', '$pulse')";
        // mysqli_query($dbConn, query: $sql22);

        $query = "SELECT * FROM DeviceLines INNER JOIN Devices ON DeviceLines.DevicesID = Devices.DevicesID LEFT JOIN Accounts ON Devices.AccountID = Accounts.AccountID WHERE DeviceLines.DevicesID = '" . $deviceId . "' AND DeviceLines.Line = '" . $line . "'";
        $result = mysqli_query($dbConn, $query);
        $deviceLine = mysqli_fetch_object($result);

        if ($pulse > 0) {
            $deviceLocationQuery = "SELECT DISTINCT l.UserID AS AccountID, l.LocationID, l.LocationName FROM Location AS l
                    JOIN Accounts AS a ON a.AccountID = l.UserID
                    JOIN UserAccount AS ua  ON (ua.AccountID = a.AccountID AND ua.LocationID = l.LocationID)
                    OR ua.ConfigurationID = 9999
                    JOIN Devices AS d ON d.AccountID = l.UserID AND d.LocationID = l.LocationID
                    WHERE l.LocationType = 1 AND d.DevicesID='" . $deviceId . "'
                    ORDER BY l.LocationName";

            $this->checkAndInsertAfterHoursPour($dbConn, $deviceLine, $line, $deviceLocationQuery, $temperature, $pressure, $dateObj, $deviceId);
        }

        if ($temperature >= $deviceLine->OptTemp + $deviceLine->TempAlertValue
            || $temperature <= $deviceLine->OptTemp - $deviceLine->TempAlertValue) {
            Log::channel('telemetry')->info('Message: ' . 'Found temperature abnormality. Adding Temp alert');
            $this->insertOrUpdateAlert($dbConn, $deviceLine, $line, '1', $temperature, $pressure, $dateObj);
        }
        if ($pressure >= $deviceLine->OptPressure + $deviceLine->PressAlertValue
            || $pressure <= $deviceLine->OptPressure - $deviceLine->PressAlertValue) {
            Log::channel('telemetry')->info('Message: ' . 'Found pressure abnormality. Adding Pressure alert');
            $this->insertOrUpdateAlert($dbConn, $deviceLine, $line, '2', $temperature, $pressure, $dateObj);
        }

        if ($tds > self::TDS_UPPER_LIMIT || $minTds > self::TDS_UPPER_LIMIT || $maxTds > self::TDS_UPPER_LIMIT) {
            Log::channel('telemetry')->info('Message: ' . 'Found TDS abnormality. Adding TDS alert');
            $this->insertOrUpdateAlert($dbConn, $deviceLine, $line, '4', $temperature, $pressure, $dateObj);
        }
    }

    /**
     * @throws Exception
     */
    public function checkAndInsertAfterHoursPour($dbConn, $deviceLine, $line, $deviceLocationQuery, $temperature, $pressure, $dateObj, $deviceId): void
    {
        $resultQuery = mysqli_query($dbConn, $deviceLocationQuery);
        if (!$resultQuery) {
            Log::channel('telemetry')->error('Message: ' . "Query error: " . mysqli_error($dbConn));
            return;
        }

        if (mysqli_num_rows($resultQuery) <= 0) {
            Log::channel('telemetry')->error('Message: ' . "No Location Found for this Device: " . $deviceId);
            return;
        }

        $row = mysqli_fetch_assoc($resultQuery);
        $locationID = $row["LocationID"];
        $dateTimeQuery = "SELECT FromDateTime, EndDateTime, TotalHours, TimeZone FROM Location WHERE LocationID = '" . $locationID . "'";
        $result = mysqli_query($dbConn, $dateTimeQuery);
        if (!$result) {
            Log::channel('telemetry')->error('Message: ' . "Query error: " . mysqli_error($dbConn));
            return;
        }

        if (mysqli_num_rows($result) <= 0) {
            Log::channel('telemetry')->error('Message: ' . "No Time Found for this Location: " . $locationID);
            return;
        }

        $row = mysqli_fetch_assoc($result);
        $fromTime = $row["FromDateTime"];
        $endTime = $row["EndDateTime"];
        $eventHis = $dateObj->format(self::TIME_FORMAT);

        if ($eventHis < $fromTime && $dateObj > $endTime) {
            Log::channel('telemetry')->info('Message: ' . 'This is an after hour pour. Adding alert');
            $this->insertOrUpdateAlert($dbConn, $deviceLine, $line, '3', $temperature, $pressure, $dateObj);
        }
    }

    /**
     * @throws Exception
     */
    public function insertOrUpdateAlert($devDb, $deviceLine, $line, $alertId, $temp, $pres, $dateTimeObject): void
    {
        $currentDate = $dateTimeObject->format(self::DATE_FORMAT);
        $currentDateTime = $dateTimeObject->format(self::DATE_TIME_FORMAT);
        $query = "SELECT DATE(AlertDateTime) as AlertDate, min_value, max_value FROM DeviceLinesAlertCurrent WHERE DevicesID = $deviceLine->DevicesID AND Line = $line AND DATE(AlertDateTime)='{$currentDate}' and AlertID=$alertId";
        $data = mysqli_query($devDb, $query);
        if ($data && mysqli_num_rows($data) > 0) {
            $row = mysqli_fetch_assoc($data);
            $min_value = $row['min_value'];
            $max_value = $row['max_value'];
            if ($alertId == 1) {
                if ($temp < $min_value) {
                    $min_value = $temp;
                }
                if ($temp > $max_value) {
                    $max_value = $temp;
                }
            } else {
                if ($pres < $min_value) {
                    $min_value = $pres;
                }
                if ($pres > $max_value) {
                    $max_value = $pres;
                }
            }
            $query = "UPDATE DeviceLinesAlertCurrent SET
                        DeviceLinesAlertStatus = '1',
                        AccountID = '{$deviceLine->AccountID}',
                        DevicesID = '{$deviceLine->DevicesID}',
                        UserAccountID = '12',
                        AlertID = '$alertId',
                        Line = '{$line}',
                        AlertCNT = AlertCNT + 1,
                        DeviceStatus = '1',
                        BeerBrandID = '{$deviceLine->BeerBrandsID}',
                        KegTypeID = '{$deviceLine->KegTypeID}',
                        DistributorID = '{$deviceLine->DistAccountID}',
                        OptTemp = '{$deviceLine->OptTemp}',
                        OptPressure = '{$deviceLine->OptPressure}',
                        TempPressAlert = '{$deviceLine->TempPressAlert}',
                        TempPressAlertTimeOut = '{$deviceLine->TempPressAlertTimeOut}',
                        TempAlertValue = '$temp',
                        min_value = '$min_value',
                        max_value = '$max_value',
                        PressAlertValue = '$pres',
                        KegCost = '{$deviceLine->KegCost}',
                        LineLength = '{$deviceLine->LineLength}',
                        AlertDateTime = '$currentDateTime',
                        RecordStatus = '{$deviceLine->RecordStatus}',
                        InsertDateTime = NOW(),
                        UpdateDateTime = NOW()
                        WHERE  DevicesID = $deviceLine->DevicesID AND Line = $line AND DATE(AlertDateTime)='{$currentDate}' and AlertID=$alertId";
        } else {
            $min_value = $alertId == 1 ? $temp : $pres;
            $max_value = $alertId == 1 ? $temp : $pres;

            $query = "INSERT INTO DeviceLinesAlertCurrent (DeviceLinesAlertStatus, AccountID, DevicesID, UserAccountID, AlertID, Line, AlertCNT, DeviceStatus, BeerBrandID, KegTypeID, DistributorID, OptTemp, OptPressure, TempPressAlert, TempPressAlertTimeOut, TempAlertValue, PressAlertValue, KegCost, LineLength, AlertDateTime, AckDateTime, RecordStatus, InsertDateTime, UpdateDateTime, min_value, max_value)
                        VALUES ('1', '{$deviceLine->AccountID}', '{$deviceLine->DevicesID}', '12', '$alertId', '{$line}', 1, '1', '{$deviceLine->BeerBrandsID}', '{$deviceLine->KegTypeID}', '{$deviceLine->DistAccountID}', '{$deviceLine->OptTemp}', '{$deviceLine->OptPressure}', '{$deviceLine->TempPressAlert}', '{$deviceLine->TempPressAlertTimeOut}', '$temp', '$pres', '{$deviceLine->KegCost}', '{$deviceLine->LineLength}', '$currentDateTime', '0000-00-00 00:00:00', '{$deviceLine->RecordStatus}', NOW(), NOW(),'$min_value','$max_value')";
        }

        if (!$devDb->query($query)) {
            Log::channel('telemetry')->error('Message: ' . "Error inserting record: " . $devDb->error);
        }
    }
}
