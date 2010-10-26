		<?php
		include ('auth.php');
?>
		
<!--
Syntax Highlighting By CodeMirror and
Copyright (c) 2008-2010 Yahoo! Inc. All rights reserved.
The copyrights embodied in the content of this file are licensed by
Yahoo! Inc. under the BSD (revised) open source license

@author Dan Vlad Dascalescu <dandv@yahoo-inc.com>

Everything Else By Hunter Dolan :)
-->
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <style>
	body {
	font-family:Arial,Helvetica,sans-serif;
	font-size:12px;
	color: #333;
	padding: 10px;
	}
	h2 {font-size:20px;}
 .CodeMirror-line-numbers {
        width: 2.2em;
        color: #aaa;
        background-color: #eee;
        text-align: right;
        padding: .4em;
        margin: 0;
        font-family: monospace;
        font-size: 10pt;
        line-height: 1.1em;
      }
  </style>
    <script src="../program/crystal/syntax/js/codemirror.js" type="text/javascript"></script>
  <body>
<?php
if ($_GET["message"] == "success") {
echo "
 <center><div id='message' style='z-index:1;overflow: none; position:absolute; top:0; left:0; background-color: #FFF; width: 100%; height:100%; font-size: 30px; color: green;'><br><br><br>Success!</div></center>
 <script src='../program/js/jquery-1.4.min.js'></script>
<script type='text/javascript'>                                         
        $(document).ready(function() {
    setTimeout(function() { $('#message').fadeOut(); }, 2000);
    });
</script>
";
}
if ($_GET['_action'] == 'save') {
$fh = fopen("../config/db.inc.php", 'w') or die("Can't open $file for writing: $php_errormsg");
if (-1 == fwrite($fh, $_POST['code'])) { die("Can't write to file:
$php_errormsg"); }
fclose($fh) or die("Can't close $file: $php_errormsg");
echo "<meta http-equiv=\"REFRESH\" content=\"0;url=db-config-editor.php?show=1&message=success\">";
die();
}
if ($_GET['show'] == '') {
echo '<iframe src="db-config-editor.php?show=1" scrolling="no" frameborder="no" height = "95%" width = "100%"></iframe>';
die();
}

?>

<?php 

$filename = "../config/db.inc.php";
// open file 
  $fh = fopen($filename, "r") or die("Error Code: ADM103 <br> Did you Move the Admin Directory? How about the config directory? Did you re-name the db.inc.php file? If error continues please check the forum."); 
// read file contents 
  $data = fread($fh, filesize($filename)) or die("Error Code: ADM103 <br> Did you Move the Admin Directory? How about the config directory? Did you re-name the db.inc.php file? If error continues please check the forum."); 
// close file 
  fclose($fh); 
// print file contents 
 echo "
 <center><h2>Editing Db.inc.php</h2>
 <div id='hide' style='border: 1px solid black; width: 920px;'>
<form action='?_action=save' method= 'post' > 
<textarea id='code' name='code' cols='100%' rows='100%'>$data</textarea> 
</div>
<br>
<center>
<input type='submit' value='Save to db.inc.php'> 
</center>
</form>"; 
?>
<script type="text/javascript">
      var editor = CodeMirror.fromTextArea('code', {
        height: "350px",
        parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "tokenizephp.js", "parsephp.js",
                     "parsephphtmlmixed.js"],
        stylesheet: ["../program/crystal/syntax/css/phpcolors.css"],
        path: "../program/crystal/syntax/js/",
        continuousScanning: 500
      });
    </script>
    </center>
