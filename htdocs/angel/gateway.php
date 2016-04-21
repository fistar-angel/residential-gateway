<?php

require_once './includes/ResidentialGatewaySettings.class.php';
require_once './includes/ResidentialGatewayInterface.class.php';
require_once './includes/WebServices.class.php';
require_once './includes/UserNotification.class.php';


/*
 * HTTP POST is the unique supported method
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    header('Accept: application/json');
    header('Content-Type: application/json');
    
    try {             
        ///////////////////////////
        //    .ini config file
        ///////////////////////////
        $st = new ResidentialGatewaySettings();
        $timezone = $st->getTimezone();
        
        //////////////////////////////
        //  CATCH DATA FROM ARDUINO
        //////////////////////////////
        
        // Read the payload of post request
        $payload = file_get_contents('php://input');
        
        error_log($payload);
           
        // No payload
        if (!$payload){
            throw new Exception("Arduino: No payload", 400, null);
        }
        $data = json_decode($payload, true);
        unset($payload);

        // Handle an invlaid JSON exception
        if (json_last_error()){
            throw new Exception("Arduino: Invalid JSON structure", 400, null);
        }
        else {
            header('X-PHP-Response-Code: 200', true, 200);
        }

        // Retrieve a list of available biologic parameters using REST API
        $rest = new WebServices();
        $bParams = $rest->getBiologicParametersList();  
        $rules = $rest->getRulesList();
        
        // Initiate a new RS object
        $orion = new ResidentialGatewayInterface();
        // get user ID
        // high priority the id in RS
        if ($st->getUserID() == ""){
            $orion->setPatientID($data["id"]);      
        }
        else {
            $orion->setPatientID($st->getUserID());
        }
        // set timezone
        $orion->setTimeZone($timezone);
        // set attributes for Orion Context Broker GE
        $orion->setAttributes($data["measurements"], $bParams, $rules);
        // create the query for Orion context broker GE
        $object = array(
                "contextElements" => array(
                    array(
                            "type" => $orion::$patientType,
                            "isPattern" => $orion::$patientPattern, 
                            "id" => $orion->getPatientID(),
                            "attributes" => $orion->getAttributes()
                        )
                    ), 
                "updateAction" => $orion::$action
            );
        $cloudPayload = json_encode($object);
        
        
        /////////////////////////////
        //  UPLOAD DATA ON CLOUD
        /////////////////////////////  
        if ($st->getDestinationIpAddr() != "" && $st->getPort() != ""){
            $orion ->setOrionContextBrokerIP($st->getDestinationIpAddr());
            $orion->setOrionContextBrokerPort($st->getPort());
        }

        $response = $orion->updateMeasurements(stripslashes($cloudPayload));
        echo $response;
        
        // Sound notification
        $orionResponseCode = 400;
        $res = json_decode($response, true);
        if (json_last_error()){
            // if error found, raise an exception
            throw new Exception("Invalid JSON object", 404, null);
        }
        if (!$res){
            // if no Orion response found, raise an exception
            throw new Exception("No Orion Context Broker GE response", 400, null);
        }
        foreach ($res as $key => $array) {
            if (isset($array)) {
                foreach ($array as $k => $v) {
                    if (isset($v["statusCode"]["code"])){
                        $orionResponseCode = $v["statusCode"]["code"];
                    }
                }
            }
        }
        
        
        ///////////////////////////
        //  ACOUSTIC NOTIFICATION
        ///////////////////////////
        $notifier = new UserNotification($orionResponseCode);
        $notifier->triggerNotification();
        
        
    } catch (Exception $ex) {
        header('X-PHP-Response-Code: '.$ex->getCode(), true, $ex->getCode());
        echo json_encode(array("error"=>array("code"=>$ex->getCode(), "reasonPhrase"=>$ex->getMessage())));
    } 
}
else {
    // server response
    header("Content-Type: application/json");
    
    header('X-PHP-Response-Code: 405', true, 405);
    echo json_encode(array("error"=>array("code"=>405, "reasonPhrase"=>"Invalid method")));
}

?>

