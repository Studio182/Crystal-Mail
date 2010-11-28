<?php
  class crystalLogin
  {
      private $rcPath;
      private $rcSessionID;
      private $rcLoginStatus;
      private $debugEnabled;
      private $debugStack;
      public function __construct($webmailPath, $enableDebug = false)
      {
          $this->debugStack = array();
          $this->debugEnabled = $enableDebug;
          $this->rcPath = $webmailPath;
          $this->rcSessionID = false;
          $this->rcLoginStatus = 0;
      }
      public function login($username, $password)
      {
          $this->updateLoginStatus();
          
          if ($this->isLoggedIn())
              $this->logout();
          
          $data = "_action=login&_user=" . urlencode($username) . "&_pass=" . urlencode($password);
          $response = $this->sendRequest($this->rcPath, $data);
          
          if (preg_match('/^Location\:.+_task=/mi', $response)) {
              $this->addDebug("LOGIN SUCCESSFUL", "RC sent a redirection to ./?_task=..., that means we did it!");
              $this->rcLoginStatus = 1;
          }
          
          elseif (preg_match('/^Set-Cookie:.+sessauth=-del-;/mi', $response)) {
              header($line);
              $this->addDebug("LOGIN FAILED", "RC sent 'sessauth=-del-'; User/Pass combination wrong.");
              $this->rcLoginStatus = -1;
          }
          
          
          else {
              $this->addDebug("LOGIN STATUS UNKNOWN", "Neither failure nor success. This maybe the case if no session ID was sent");
              throw new crystalLoginException("Unable to determine login-status due to technical problems.");
          }
          return $this->isLoggedIn();
      }
      public function isLoggedIn()
      {
          $this->updateLoginStatus();
          if (!$this->rcLoginStatus)
              throw new crystalLoginException("Unable to determine login-status due to technical problems.");
          return($this->rcLoginStatus > 0) ? true : false;
      }
      public function logout()
      {
          $data = "_action=logout&_task=logout";
          $this->sendRequest($this->rcPath, $data);
          return !$this->isLoggedIn();
      }
      public function redirect()
      {
          header("Location: {$this->rcPath}");
          exit;
      }
      private function updateLoginStatus($forceUpdate = false)
      {
          if ($this->rcSessionID && $this->rcLoginStatus && !$forceUpdate)
              return;
          
          if ($_COOKIE['crystalmail_sessid'])
              $this->rcSessionID = $_COOKIE['crystalmail_sessid'];
          
          $response = $this->sendRequest($this->rcPath);
          
          if (preg_match('/<input.+name="_pass"/mi', $response)) {
              $this->addDebug("NOT LOGGED IN", "Detected that we're NOT logged in.");
              $this->rcLoginStatus = -1;
          } elseif (preg_match('/<div.+id="message"/mi', $response)) {
              $this->addDebug("LOGGED IN", "Detected that we're logged in.");
              $this->rcLoginStatus = 1;
          } else {
              $this->addDebug("UNKNOWN LOGIN STATE", "Unable to determine the login status. Did you change the RC version?");
              throw new crystalLoginException("Unable to determine the login status. Unable to continue due to technical problems.");
          }
          
          if (!$this->rcSessionID) {
              $this->addDebug("NO SESSION ID", "No session ID received. RC version changed?");
              throw new crystalLoginException("No session ID received. Unable to continue due to technical problems.");
          }
      }
      private function sendRequest($path, $postData = false)
      {
          $method = (!$postData) ? "GET" : "POST";
          $port = ($_SERVER['HTTPS']) ? 443 : 80;
          $host = ($port == 443) ? "ssl://localhost" : "localhost";
          
          $cookies = array();
          foreach ($_COOKIE as $name => $value)
              $cookies[] = "$name=$value";
          
          if (!$_COOKIE['crystalmail_sessid'] && $this->rcSessionID)
              $cookies[] = "crystalmail_sessid={$this->rcSessionID}";
          $cookies = ($cookies) ? "Cookie: " . join("; ", $cookies) . "\r\n" : "";
          
          if ($method == "POST") {
              $request = "POST " . $path . " HTTP/1.1\r\n" . "Host: " . $_SERVER['HTTP_HOST'] . "\r\n" . "Content-Type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($postData) . "\r\n" . $cookies . "Connection: close\r\n\r\n" . $postData;
          }
          
          else {
              $request = "GET " . $path . " HTTP/1.1\r\n" . "Host: " . $_SERVER['HTTP_HOST'] . "\r\n" . $cookies . "Connection: close\r\n\r\n";
          }
          
          $fp = fsockopen($host, $port);
          
          $this->addDebug("REQUEST", $request);
          fputs($fp, $request);
          
          $response = "";
          while (!feof($fp)) {
              $line = fgets($fp, 4096);
              
              if (preg_match('/^HTTP\/1\.\d\s+404\s+/', $line))
                  throw new crystalLoginException("No crystal installation found at '$path'");
              
              if (preg_match('/^Set-Cookie:.+crystalmail_sessid=([^;]+);/i', $line, $match)) {
                  header($line);
                  $this->addDebug("GOT SESSION ID", "New session ID: '$match[1]'.");
                  $this->rcSessionID = $match[1];
              }
              $response .= $line;
          }
          fclose($fp);
          $this->addDebug("RESPONSE", $response);
          return $response;
      }
      private function addDebug($action, $data)
      {
          if (!$this->debugEnabled)
              return false;
          $this->debugStack[] = sprintf("<b>%s:</b><br /><pre>%s</pre>", $action, htmlspecialchars($data));
      }
      public function dumpDebugStack()
      {
          print "<p>" . join("\n", $this->debugStack) . "</p>";
      }
  }
  class crystalLoginException extends Exception
  {
  }

$rcl = new crystalLogin("/Crystal-Mail/", true);
 
try {
   # If we are already logged in, simply redirect
   if ($rcl->isLoggedIn())
     // $rcl->redirect();
 $POST = $GET;
   # If not, try to login and simply redirect on success
   $rcl->login($_POST['user'], $_POST['pass']);
 
   if ($rcl->isLoggedIn())
      $rcl->redirect();
   # If the login fails, display an error message
   die("ERROR: Login failed due to a wrong user/pass combination.");
}
catch (crystalLoginException $ex) {
   echo "ERROR: Technical problem, ".$ex->getMessage();
   $rcl->dumpDebugStack(); exit;
   }