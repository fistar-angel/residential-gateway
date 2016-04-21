<?php

/**
 * Description of HttpBaseMethods
 *
 * @author panagiotis
 */
class HttpBaseMethods {
    
    public function __construct() {}

    /**
     * Execute a HTTP GET request and return its response
     * @param string $host          The IP address of the destination server
     * @param integer $port         The available port of the server (web service)
     * @param string $urlPath       The 
     * @param type $headersList     The list of used headers
     * @return string|array         The response of destination server
     */
    public function httpGetMethod($host, $port, $urlPath, $headersList){  
        // Define the URL
        $url = "http://";
        $url .= $host;
        $url .= ":";
        $url .= $port;
        $url .= $urlPath;
                
        // Initialize a new session and return a cURL handle 
        $handler = curl_init( $url );
        
        // Set the headers of your interest
        curl_setopt( $handler, CURLOPT_HTTPHEADER, $headersList);
        
        // Return the transfer as a string of the return value 
        curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
        
        // Send the http request and get the response
        $response = curl_exec( $handler );
        
        // Close the session
        curl_close( $handler );
        
        // server response
        return $response;
        
    }
    
    
    /**
     * Execute a HTTP POST request and return its response
     * @param string $host          The IP address of the destination server
     * @param integer $port         The available port of the server (web service)
     * @param string $urlPath       The 
     * @param string|array $payload The payload of teh post request
     * @param type $headersList     The list of used headers
     * @return string|array         The response of destination server
     */
    public function httpPostMethod($host, $port, $urlPath, $payload, $headersList){
        // Define the URL
        $url = "http://";
        $url .= $host;
        $url .= ":";
        $url .= $port;
        $url .= $urlPath;
        
        // Initialize a new session and return a cURL handle 
        $handler = curl_init( $url );
               
        // Set the payload on the given cURL session handle
        curl_setopt( $handler, CURLOPT_POSTFIELDS, stripslashes($payload));
        
        // Set the headers of your interest
        curl_setopt( $handler, CURLOPT_HTTPHEADER, $headersList);
        
        // Return the transfer as a string of the return value 
        curl_setopt( $handler, CURLOPT_RETURNTRANSFER, true );
                
        // Send the http request and get the response
        $response = curl_exec( $handler );
        
        // Close the session
        curl_close( $handler );
        
        // server response
        return $response;
    }
 
    
    /**
    * HTTP Server response (status, reason)
    * @param integer $status
    * @return array
    */
    public function httpServerResponse($status){

        $response = '';

        switch ($status) {
            case 200:
                header('X-PHP-Response-Code: 200', true, $status);
                header('Content-Type: application/json');
                //$response = array("response" => array("status"=>"$status", "reason"=>"OK"));            
                break;
            case 404:
                header('X-PHP-Response-Code: 404', true, $status);
                header('Content-Type: application/json');
                //$response = array("response" => array("status" => "$status", "reason" => "Invalid JSON structure"));
                break; 
            case 405:
                header('X-PHP-Response-Code: 405', true, $status);
                header('Content-Type: application/json');        
                //$response = array("response" => array("status" => "$status", "reason" => "Method not allowed"));
                break;
            case 500:
                header('X-PHP-Response-Code: 500', true, $status);
                header('Content-Type: application/json');        
                //$response = array("response" => array("status" => "$status", "reason" => "Internal error"));
                break;
            default:
                break;
        }
    }
    
}


?>