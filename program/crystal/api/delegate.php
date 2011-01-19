<?php
//Just to be safe make sure this script can only be run by the index.php
if($allowapi == "true") {

//No matter what happens we are responding in json... might as well set the mime type now
header('Content-Type: application/json; charset=UTF-8');

//Mode Switch (this is where all the magic happens!
switch ($_GET['action']) {
    case 'login':
//If we are loggin in run the login script
require_once("login.php");
        break;
}




//STAY DOWN!!
}
?>