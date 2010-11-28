<?php

$_POST = $_GET;

if (isset($_GET['qr_code'])) {
header('Content-Type: image/png');
//Get current API URI

 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
$pageURL = str_replace("?qr_code&user=".$_GET['user'], "?" , $pageURL);

//Add API Data
$pageURL .= 'filldata&user='.$_GET['user'];

/* make a URL small */
function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
	//create the URL
	$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
	
	//get the url
	//could also use cURL here
	$response = file_get_contents($bitly);
	
	//parse depending on desired format
	if(strtolower($format) == 'json')
	{
		$json = @json_decode($response,true);
		return $json['results'][$url]['shortUrl'];
	}
	else
	{
		$xml = simplexml_load_string($response);
		return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
	}
}
$pageURL = make_bitly_url($pageURL,'crystalmail','R_d0218daae076ded41eb8bf40fe930cae','json');

//Echo QR Code
echo file_get_contents('http://qrcode.kaywa.com/img.php?s=20&d='.urlencode($pageURL));
}

//Convert QR Code API to JSON

if (isset($_GET['filldata'])) {
include ('../config/main.inc.php');
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
$pageURL = str_replace("api/?filldata&user=".urlencode($_GET['user']), "" , $pageURL);
$pageURL = str_replace("api/?filldata&user=".$_GET['user'], "", $pageURL);
$json = array('name' => $cmail_config['product_name'],
'json_api_uri' => $pageURL,
'username' => $_GET['user'],
'api_version' => '1.0');
$json = json_encode($json);
echo $json;
}


//
// Get Messages
//
