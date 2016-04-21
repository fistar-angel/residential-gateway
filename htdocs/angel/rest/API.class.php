<?php


require_once '../includes/ResidentialGatewaySettings.class.php';

/**
 * Description of API
 *
 * @author panagiotis
 */

class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    private $methodAvailability = array(
            "method" => null,
            "code" => null,
            "phrase" => null
        );
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /configuration
     */
    private $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    private $verb = null;
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    private $args = null;
    /**
     * Property: payload
     * Keeps the input of the POST request
     */
    private $payload = null;
    
    
    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("Accept: application/json");
        header("Content-Type: application/json");
        
        $this->args = explode('/', rtrim($request, '/'));

        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        
        //$this->endpoint = array_shift($this->args);  
        $this->endpoint = $request;
       
        // Filter the request based on HTTP methods
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->setMethodAvailability('GET', 200, "OK");
                break;
            case 'POST':
                $this->setMethodAvailability('POST', 200, "OK");
                $this->payload = file_get_contents("php://input");
                error_log($this->payload);
                break;
            case 'PUT':
                $this->setMethodAvailability('PUT', 405, "Invalid method");
                break;
            case 'DELETE':
                $this->setMethodAvailability('DELETE', 405, "Invalid method");
                break;
            default:
                $this->setMethodAvailability($_SERVER['REQUEST_METHOD'], 405, "Invalid method");
                break;
        }
    }
    
    
    
    //////////////////
    //   GETTERS
    //////////////////
    public function getMethodAvailability(){
        return $this->methodAvailability["code"];
    }
    
    public function getMethod(){
        return $this->methodAvailability["method"];
    }
    
    public function getPayload(){
        return $this->payload;
    }
    
    
    //////////////////
    //   SETTERS
    //////////////////
    public function setMethodAvailability($_method, $_code, $_phrase){
        $this->methodAvailability['method'] = $_method;
        $this->methodAvailability['code'] = $_code;
        $this->methodAvailability['phrase'] = $_phrase;
    }
    
    
    //////////////////
    //   METHODS
    //////////////////
    
    /**
     * Check if the class endpoint method exists
     * @return type
     */
    public function resourceExistance() {
        if ((int)method_exists($this, $this->endpoint) > 0) {
            return $this->getResponse($this->{$this->endpoint}($this->args));
        }
        return $this->getResponse(array("error"=>array("code"=>"404","reasonPhrase"=>"Not found", "details"=>"Invalid endpoint")), 404);
    }

    
    /**
     * Return the response of the server 
     * @param string $data  A message
     * @param type $status  HTTP status
     * @return json         
     */
    private function getResponse($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->getResponseReason($status));
        return json_encode($data);
    }

    
    /**
     * Return the reason phrase for http status code
     * @param integer $code The HTTP status code
     * @return string       The HTTP reason phrase
     */
    private function getResponseReason($code) {
        $status = array(  
            200 => 'OK',
            404 => 'Not Found',   
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ); 
        return ($status[$code])?$status[$code]:$status[500]; 
    }
      
    
    /**
     * Configuration endpoint
     */
    public function configuration(){
        $cf = new ResidentialGatewaySettings();
        
        // GET or POST /configuration
        if ($this->methodAvailability["method"] == 'GET') {
            $response = json_encode($cf->getConfigurationFile());
        }
        else if ($this->methodAvailability["method"] == 'POST') {
            $data = json_decode($this->payload, true);
            $cf->setUserID($data["userID"]);
            $cf->setDeviceID($data["deviceID"]);
            $cf->setDestinationIpAddr($data["ip"]);
            $cf->setPort($data["port"]);
            $cf->setTimezone($data["timezone"]);
            $cf->writeConfigFile();

            $response = json_encode($cf->getConfigurationFile());
        }
        return $response;
    }
}


?>