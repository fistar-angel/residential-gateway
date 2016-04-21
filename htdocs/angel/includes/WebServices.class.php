<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/angel/includes/HttpBaseMethods.class.php';

/**
 * Description of WebServices
 * This class provides methods for rest calls.
 * @author panagiotis
 */
class WebServices extends HttpBaseMethods{
    /////////////////////////////
    //  Properties definition
    /////////////////////////////
    
    // VM information 
    private static $vmIP = "130.206.82.133";
    private static $vmPort = 5000; 
    private $path = array(
        "RULES" => "/rules",
        "BIOLOGIC_PARAMETERS" => "/biologic_parameters"
    );  
    
    
    /////////////////////////////
    //      Constructor
    /////////////////////////////
    
    public function __construct(){
        // empty
    }
    
    
    //////////////////////////////
    //          GETTERS
    //////////////////////////////
    
    public function getRestInterfaceIP(){
        return self::vmIP;
    }
    
    public function getRestInterfacePort(){
        return self::$vmPort;
    }  
    
    public function getRestInterfacePath($_input){
        return $this->path[$_input];
    }
    
    public function getBiologicParametersList(){
        $headersList = array('Content-Type:application/json', 'Accept:application/json');
        return parent::httpGetMethod(self::$vmIP, self::$vmPort, $this->path["BIOLOGIC_PARAMETERS"], $headersList);
    }

    public function getRulesList(){
        $headersList = array('Content-Type:application/json', 'Accept:application/json');
        return parent::httpGetMethod(self::$vmIP, self::$vmPort, $this->path["RULES"], $headersList);
    }
}

