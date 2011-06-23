<?php
//Just to be safe make sure this script can only be run by the index.php
if($allowapi == "true") {

//No matter what happens we are responding in json... might as well set the mime type now
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With,X-File-Name');  

//Mode Switch (this is where all the magic happens!
switch ($_GET['action']) {
    case 'login':
//If we are loggin in run the login script
require_once("login.php");
        break;
    case 'qr_code':
require_once("qr.php");
		break;
	case 'list':
require_once("list.php");
break;
	case 'calendar':
require_once("cal.php");
break;
	case 'internal':
//If its internal well do everything in the script... we just want to get the headers.
break;
	default: 
	echo json_encode(array('error' => '003', 'human_error' => 'Function Doesn\'t Exists!'));
}
}
?>