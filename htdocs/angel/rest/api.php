<?php

require_once './API.class.php';
require_once '../includes/ResidentialGatewaySettings.class.php';

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $api = new API($_REQUEST['request']);
    
    // Check if the http method is valid
    if ($api->getMethodAvailability() != 200){
        header("HTTP/1.1 405 Invalid method");
        echo json_encode(array("error"=> array("code"=>"405","reasonPhrase"=>"Invalid method", "details"=>"Only GET/POST methods are valid")));
        return;
    }
    
    // check if the required resourse exists
    $message = $api->resourceExistance();
    if (http_response_code() != 200){
        echo $message;
    }
    else {
        //
        $pr =  $api->configuration();
        echo $pr;
    }
    
} catch (Exception $e) {
    echo json_encode($e->getMessage());
}


?>

