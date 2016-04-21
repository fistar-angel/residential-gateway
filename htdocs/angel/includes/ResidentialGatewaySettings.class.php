<?php

/**
 * Description of ResidentailGatewaySettings
 * This class handles the angel configuration file (.ini)
 * @author panagiotis
 */
class ResidentialGatewaySettings {
    
    /////////////////////
    //    PROPERTIES
    /////////////////////
    const location = "C:/xampp/xampp/htdocs/angel/config_angel.ini";
    private $settings;
    
    
    /////////////////////
    //   CONSTRUCTOR
    /////////////////////
    public function __construct() {
        $this->settings = parse_ini_file(self::location, true);
    }
       
    
    /////////////////////
    //     GETTERS
    /////////////////////
    public function getConfigurationFile(){
        return $this->settings;
    }    
    
    public function getUserID() {
        return $this->settings["settings"]["userID"];
    }
    
    public function getTimezone(){
        return $this->settings["settings"]["timezone"];
    }
    
    public function getDeviceID() {
        return $this->settings["settings"]["deviceID"];
    }
    
    public function getDestinationIpAddr() {
        return $this->settings["settings"]["ip"];
    }

    public function getPort() {
        return $this->settings["settings"]["port"];
    }
    
    /////////////////////
    //     SETTERS
    /////////////////////
    public function setUserID($uid) {
        $this->settings["settings"]["userID"] = $uid;
    }
    
    public function setTimezone($timezone) {
        $this->settings["settings"]["timezone"] = $timezone;
    }
    
    public function setDeviceID($deviceID) {
        $this->settings["settings"]["deviceID"] = $deviceID;
    }
    
    public function setDestinationIpAddr($IP){
        $this->settings["settings"]["ip"] = $IP;
    }

    public function setPort($port){
        $this->settings["settings"]["port"] = $port;
    }
    
    /////////////////////
    //     METHODS
    /////////////////////
    public function writeConfigFile() {
        // Contain the content of config file
        $temp = array();
        
        // Loop through the array of arrays
        foreach($this->settings as $key => $val) {
            if(is_array($val)) {
                $temp[] = "[$key]";
                foreach($val as $skey => $sval) {
                    $temp[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
                }
            }
            else {
                $temp[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
            }
        }
        
        $this->secureRewriteConfigFile(implode("\r\n", $temp));
    }

    private function secureRewriteConfigFile($data) {
        
        if ($fp = fopen(self::location, 'w')) {
            $startTime = microtime();
            
            do {            
                $canWrite = flock($fp, LOCK_EX);
                if(!$canWrite) {
                    usleep(round(rand(0, 400)*1000));
                }
            } while ((!$canWrite) && ((microtime()-$startTime) < 1000));

            //file was locked so now we can store information
            if ($canWrite) {
                fwrite($fp, $data);
                flock($fp, LOCK_UN);
            }
            
            // close file
            fclose($fp);
        }
    }
}
