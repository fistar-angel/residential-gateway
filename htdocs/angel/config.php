<!DOCTYPE html>

<?php 
    require_once './includes/Timezone.class.php';
    require_once './includes/ResidentialGatewaySettings.class.php';
    
    // access the config file .ini
    $cf = new ResidentialGatewaySettings();
    $uid = $cf->getUserID();
    $did = $cf->getDeviceID();
    $ip = $cf->getDestinationIpAddr();
    $port = $cf->getPort();
    $tm = $cf->getTimezone();
?>

<html>
    <head>
        <title>ANGEL | Configuration</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="FI-STAR/Singular Logic SA/Angel">
        <meta name="author" content="Panagiotis Athanasoulis">
        <link href="./libraries/css/bootstrap.min.css" rel="stylesheet">
        <link href="./libraries/css/custom.css" rel="stylesheet">
        <link href="./libraries/css/sticky-footer-navbar.css" rel="stylesheet">
        <link href="./libraries/css/font-awesome.min.css" rel="stylesheet">
    </head>
    <body>        
        <div id="wrap">
            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
               <div class="container">  
                   <a class="navbar-brand" href="#">Configuration</a>
                </div>
            </nav>
            
            <div class="container">     
                <br>
                <div class="tabbable">
                    <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                        <li class="active"><a href="#settings" data-toggle="tab" style="font-size: 14pt; color: #039"><span class="fa fa-cogs"></span> Settings<span class="badge pull-right"></span></a></li>
                    </ul>
                    <div id="my-tab-content" class="tab-content" >                
                        <div class="tab-pane active" id="settings" style="color:black; border-bottom:1px solid #ddd; border-left:1px solid #ddd; border-right:1px solid #ddd; border-radius: 5px"> 
                            <br>
                            <div class="row">
                                <div class="col-sm-2 col-xs-12"></div>
                                <div class="col-sm-2 col-xs-12 pull-right" style="color: #468847">
                                    <span id="refresh"><i class="fa fa-refresh"></i> Refresh</span>
                                </div>
                            </div>
                            <br>
                            <div class="row">    
                                <form name ="ConfigForm" onsubmit="return updateSettings()" action="" method="post">
                                    <div class="row">
                                        <div class="col-sm-2"></div>
                                        <div class="col-xs-12 col-sm-10 col-md-8">  
                                            <!-- set uid -->
                                            <div class="row">
                                               <div class="col-xs-12 col-sm-4">
                                                   <label>User ID</label>
                                                </div>
                                                <div class="col-xs-12 col-sm-8 input-group">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-user fa-fw"></span>
                                                    </span>
                                                    <input type="text" class="form-control" id="uid" value="<?php echo $uid; ?>" maxlength="40" placeholder="set the user id" required autofocus />
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <!-- set device id -->
                                            <div class="row">
                                               <div class="col-xs-12 col-sm-4">
                                                   <label>Device ID</label>
                                                </div>
                                                <div class="col-xs-12 col-sm-8 input-group">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-tag fa-fw"></span>
                                                    </span>
                                                    <input type="text" class="form-control" id="did" value="<?php echo $did; ?>" name="device_id" maxlength="40" placeholder="set the device id" required />
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <!-- set destination IP adress -->
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                   <label>IP address</label>
                                                </div>
                                                <div class="col-xs-12 col-sm-4 input-group">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-at fa-fw"></span>
                                                    </span>
                                                    <input type="text" class="form-control" id="ip" value="<?php echo $ip; ?>" name="ip_address" maxlength="40" placeholder="set the destination IP address" required />
                                                </div>
                                                <div class="col-xs-12 col-sm-1">
                                                   <label>Port</label>
                                                </div>
                                                <div class="col-xs-12 col-sm-3 input-group">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-shield fa-fw"></span>
                                                    </span>
                                                    <input type="number" min="1" max="65535" class="form-control" id="port" value="<?php echo $port; ?>" name="ip_port" maxlength="40" placeholder="port" required />
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <!-- set timezone -->
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-4">
                                                   <label>Timezone</label>
                                                </div>
                                                <div class="col-xs-12 col-sm-8 input-group">
                                                    <span class="input-group-addon"> 
                                                        <span class="fa fa-clock-o fa-fw"></span> 
                                                    </span>
                                                    <select class="selectpicker form-control"  name="timezone" id="timezone" title="timezone"> 

                                                        <?php
                                                            if (isset($tm) && $tm != ""){
                                                                $options = '<option value="Select your time zone from the list..." disabled="disabled">Select your time zone from the list...</option>';
                                                                foreach(Timezone::getInstance()->getTimeZonesList() as $key => $t) {
                                                                    $options .= ($t['zone'] == $tm) ? '<option value="'. $t["zone"].'" selected="selected">'. $t["zone"] . '</option>' : '<option value="'. $t["zone"].'">'. $t["zone"] . '</option>';
                                                                } 
                                                                echo $options;
                                                            }
                                                            else {
                                                                echo '<option value="Select your time zone from the list..." disabled="disabled" selected="selected">Select your time zone from the list...</option>';
                                                            }
                                                        ?>   
                                                    </select>
                                                </div>
                                            </div>
                                            <br>
                                            <br>
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6"></div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <button type="submit" class="btn btn-primary" id="submit-btn" value="submit" name = "Submit" style="width: 100px">
                                                        <span class="fa fa-check"></span>  Submit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <br>
                            </div>
                        </div>  
                    </div> 
                </div>  
            </div>
        </div>
        <div id="footer" style="text-align: center">
            <div class="container responsive_font">
                <br>
                Singular Logic SA. Copyright <i class="fa fa-copyright"></i> 2014-2015
            </div>
        </div>
    
        <script src="./libraries/js/jquery-1.10.2.min.js"></script>
        <script src="./libraries/js/bootstrap.min.js"></script>
        <script src="./libraries/js/bootbox.min.js"></script>
        <script src="./libraries/js/jquery.blockUI.js"></script>
        <script src="./libraries/js/jquery-ui-1.10.3.min.js"></script>
        <script type="text/javascript"> 

            $(document).ready(function(){                
                
                // Underline that the refresh area is clickable
                $("#refresh").css('cursor', 'pointer');
                
                // refresh page on demand
                $("#refresh").click(function(){
                    $(this).find("i").addClass("fa-spin fa-lg");
                    
                    // retrieve file
                    $.ajax({
                        type: 'GET',
                        url: "./configuration",
                        dataType: "json",
                        contentType: "application/json; charset=utf-8",
                        success: function (response) {
                            console.log("Success HTTP GET request");
                            //response = JSON.parse(data);
                            $("#uid").attr("value", response["settings"]["userID"]);
                            $("#did").attr("value", response["settings"]["deviceID"]);
                            $("#ip").attr("value", response["settings"]["ip"]);
                            $("#port").attr("value", response["settings"]["port"]);
                            $("#timezone").attr("value", response["settings"]["timezone"]);
                        },
                        error: function () {
                            alert("Oops. One error is occured!!");
                            window.location.href = './config.php';   
                        }
                    });
                });
                
                // If the user clicks on submit button, the icon is changed
                $("#submit-btn").click(function(){
                   $(this).find("span").removeClass("fa-check").addClass("fa-spinner fa-spin fa-lg"); 
                });
            });
            
            
            // Check the form validity and upload data
            function updateSettings(){
                
                if (ipAddressValidation($("#ip").val()) === false){
                    alert("Please type a valid IP address");
                    return false;
                }
                
                // user inpouts
                var object = {
                    userID: $("#uid").val(),
                    deviceID: $("#did").val(),
                    ip: $("#ip").val(),
                    port: $("#port").val(),
                    timezone: $("#timezone").val()
                };

                // update file
                $.ajax({
                    type: 'POST',
                    url: "./configuration",
                    dataType: "json",
                    contentType: "application/json; charset=utf-8",
                    data: JSON.stringify(object),
                    success: function (response) {
                        console.log("Success HTTP POST request");
                    },
                    error: function () {
                        alert("Oops. One error is occured!!");
                        window.reload();    
                    }
                });
                
                return false;
            }
            
            // Check if IP address of user is valid or not
            function ipAddressValidation(_ip){
                var ipRegex = /^(([01]?[0-9]?[0-9]|2([0-4][0-9]|5[0-5]))\.){3}([01]?[0-9]?[0-9]|2([0-4][0-9]|5[0-5]))$/;
                var state = true;
                if (!ipRegex.test(_ip) && _ip !=="") { 
                    state = false;
                }
                return state;
            }
            
            // Prevent user interaction while ajax request is executing
            $(document).ajaxStart(function() {
                $("#submit-btn").addClass("disabled");
                $(document).find("#refresh").addClass("disabled");
            })
            .ajaxStop(function(){
                setTimeout(function(){
                    $("#submit-btn").removeClass("disabled");
                    $("#submit-btn").find("span").removeClass("fa-spinner fa-spin fa-lg").addClass("fa-check");
                    $(document).find("#refresh").removeClass("disabled");
                    $(document).find("#refresh").find("i").removeClass("fa-spin fa-lg");
                }, 2000);  
            });
            
               
        </script>
        
    </body>
</html>
