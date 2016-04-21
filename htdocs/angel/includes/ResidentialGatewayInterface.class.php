<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/angel/includes/HttpBaseMethods.class.php';


/**
 * Description of ResidentialGatewayInterface
 * This class provides methods for the residential gateway.
 * @author panagiotis
 */
class ResidentialGatewayInterface extends HttpBaseMethods{
    /////////////////////////////
    //  Properties definition
    /////////////////////////////
    
    // orion settings
    private $ContextBrokerIP;
    private $ContextBrokerPort; 
    // patient structure
    private $patientID;
    public static $patientPattern = "false";
    public static $patientType = "Patient";
    // attributes
    private $attributes;
    //time
    private $timezone;
    // action
    public static $action = "APPEND";
    // operation
    private $operation = array(
        "QUERY" => "/ngsi10/queryContext",
        "UPDATE" => "/ngsi10/updateContext",
        "SUBSCRIBE" => "/ngsi10/subscribeContext",
        "UPDATE_SUBSCRIPTION" => "/ngsi10/updateContextSubscription",
        "UNSUBSCRIBE" => "/ngsi10/unsubscribeContext"       
        );


    /////////////////////////////
    //      Constructor
    /////////////////////////////
    
    public function __construct(){
        $this->ContextBrokerIP = "130.206.82.133";
        $this->ContextBrokerPort = "1026";  
    }
    
    
    //////////////////////////////
    //          SETTERS
    //////////////////////////////
    
    public function setOrionContextBrokerIP($_ip){
        $this->ContextBrokerIP = $_ip;
    }
    
    public function setOrionContextBrokerPort($_port){
        $this->ContextBrokerPort = $_port;
    }
    
    
    public function setPatientID($_id){
        $this->patientID = $_id;
    }
    
    public function setPatientPattern($_pattern) {
        $this->patientPattern = $_pattern;
    }
    
    /**
    * Create a list of attributes on the Orion Context broker format
    * @param array $json   The list of measurements
    * @return array        The parsed list of measurements
    */
    public function setAttributes($measurements, $bParameters, $rules){
        $_attrs = array();
        
        // use rest call
        $bParametersList = json_decode($bParameters, true);
        // rules
        $rulesList = json_decode($rules, true);

        // send all parameters. If no values exist, null.
        $mLen = sizeof($measurements);
        $tmz = true;
        foreach ($bParametersList["response"]["biologic_parameters"] as $index => $parameter) {
            $counter = 0;
            foreach ($measurements as $name => $v) {
                $value = $v/100;
                $counter++;
                // append measurements
                if ($parameter["name"] == $name) {
                    // validate values
                    $validFlag = True;
                    foreach ($rulesList['response']['rules'] as $i => $rule) {
                        if ($rule["biological_parameter_name"] != $name){
                            continue;
                        }
                        if ($rule['acceptable_low_threshold'] > $value || $rule['acceptable_high_threshold'] < $value){
                            $validFlag = False;
                        }
                    }                   
                    if ($validFlag){
                        $record = array("name" => $name, "type" => $parameter["unit"], "value" => $value);
                        array_push($_attrs, $record);
                    }
                    else {
                        $record = array("name" => $parameter["name"], "type" => $parameter["unit"], "value" => null);
                        array_push($_attrs, $record);
                    }
                    break;
                }
                // append null values of inexistance parameters
                if ($counter == $mLen){
                    $record = array("name" => $parameter["name"], "type" => $parameter["unit"], "value" => null);
                    array_push($_attrs, $record);
                    break;
                }
            }
        }
        
        // append date time
        $datetime = $this->callTimeService();
        if (isset($datetime)){
            array_push($_attrs, array("name" => "timestamp", "type" => $this->getTimeZone(), "value" => $datetime));
        }
        
        $this->attributes = $_attrs; 
    }
    
    public function setTimeZone($_timezone){
        $this->timezone = $_timezone;
    }


    
    //////////////////////////////
    //          GETTERS
    //////////////////////////////
    
    public function getOrionContextBrokerIP(){
        return $this->ContextBrokerIP;
    }
    
    public function getOrionContextBrokerPort(){
        return $this->ContextBrokerPort;
    }
            
    public function getPatientID(){
        return $this->patientID;
    }
    
    public function getPatientPattern(){
        return $this->patientPattern;
    }
    
    public function getPatientType() {
        return $this->patientType;
    }
    
    public function getAttributes(){
        return $this->attributes;
    }
    
    public function getTimeZone(){
        return $this->timezone;
    }
    
    public function getOrionAction(){
        return $this->action;
    }
    
    public function getOperation($_input){
        return $this->operation[$_input];
    }
   
    
    //////////////////////////////
    //          METHODS
    //////////////////////////////
    
    /**
     * Upload measurements on Orion Context Broker GE
     * @param json $payload
     * @return json
     */
    public function updateMeasurements($payload){
        $headersList = array('Content-Type:application/json', 'Accept:application/json');
        return parent::httpPostMethod($this->ContextBrokerIP, $this->ContextBrokerPort, $this->operation["UPDATE"], $payload, $headersList);
    }
    
    /**
     * Call the RESTful Time Service SE to get the real timestamp based on user timezone
     * @param type $timezone
     * @return type
     */
    private function callTimeService(){
        $headersList = array('Content-Type:application/x-httpd-php');
        //$headersList = array('Content-Type: text/html');
        $urlPath = "/time-service/datetime?mode=details&timezone=".$this->timezone;
        $response =  parent::httpGetMethod("147.27.50.135", "80", $urlPath, $headersList);
        $res = json_decode($response, true);
        return $res["DateTime"];
    }
}

