<?php


   if($allowapi == true) {
   //If the user or pass fields aren't filled in... what are we doing? Just kill the whole thing with an error code of blankfields
   if (!isset($_POST['user']) or !isset($_POST['pass'])) {
   die(json_encode(array('error' => 'blankfields')));
   }
   
   $cmail = cmail::get_instance();
   
   $auth['user'] = $_POST['user'];
   $auth['pass'] = $_POST['pass'];
   
   if(!$cmail->login($auth['user'], $auth['pass'], '')) {
   die(json_encode(array('error' => 'auth')));
   }
    // create new session ID
    $cmail->session->remove('temp');
    $cmail->session->regenerate_id();

    // send auth cookie if necessary
    $cmail->authenticate_session();

    // log successful login
    cmail_log_login();
    //Congrats were in! Just send the client the auth token.
    die(json_encode(array('error' => 'false', 'token' => $cmail->get_request_token())));
    }
    

?>