<?php
// Define the validation function
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
    file_put_contents($file, $responseBody, FILE_APPEND);
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
$file = 'requests.log';
file_put_contents($file, $request, FILE_APPEND);
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
                new class {
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
?>



