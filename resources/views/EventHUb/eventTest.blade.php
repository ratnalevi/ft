<?php
echo file_get_contents("requests.log");


// $request = $_SERVER['REQUEST_METHOD'] . ' ' . $_SERVER['REQUEST_URI'] . ' ' . $_SERVER['SERVER_PROTOCOL'] . "\n";

// $bodylog = file_get_contents('php://input');

// $request .= 'Request Body: ' . $bodylog . "\n\n";

// $file = 'requests.log';
// file_put_contents($file, $request, FILE_APPEND);

// try{

// // Handle incoming requests
// if ($_SERVER["REQUEST_METHOD"] === "POST") {
//   // Check if this is a validation request
//   $body = file_get_contents("php://input");
//   $data = json_decode($body, true);
//   $eventType = $data[0]["eventType"];
//   if ($eventType === "Microsoft.Devices.DeviceTelemetry") {
//     // Handle data


//     $bodyTag = $data->data->body;

//     $decoded_string = base64_decode($bodyTag);
//     // this will be the json that you can apply your json on and then insert in database

//   } else {
//     // ...
//   }
// }}
// catch(Exception $e) {
//   $error= 'Message: ' .$e->getMessage();

//   $file = 'requests.log';
//  file_put_contents($file, $request, FILE_APPEND);

// }
?>

