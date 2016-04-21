<?php

/**
 * Description of UserNotification
 * This class triggers a acoustic feedback to the user/potential faller
 * @author panagiotis
 */
class UserNotification {
    
    // properties
    private $status;
    private $file;
    private $fileList = array(
            '2' => "notify_2xx.bat", 
            '4' => "notify_4xx.bat", 
            '5' => "notify_5xx.bat"
        );
    
    ///////////////////////
    //    CONSTRUCTOR
    ///////////////////////
    public function __construct($_status) {
        $this->status = $_status;
        $this->setFile();
    }
    
    ///////////////////////
    //      SETTERS
    ///////////////////////
    public function setFile(){
        $this->file = $this->fileList[substr(strval($this->status), 0, 1)];
    }
   
    ///////////////////////
    //      GETTERS
    ///////////////////////
    public function getFile(){
        return $this->file;
    }
    
    
    ///////////////////////
    //      METHODS
    ///////////////////////
    public function triggerNotification() {
        exec($this->file);        
    }
}
