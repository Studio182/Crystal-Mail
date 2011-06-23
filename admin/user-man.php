<?php
include ('auth.php');
if ($_GET['show'] == '') {
echo '<iframe src="user-man.php?show=1" scrolling="auto" frameborder="no" height = "100%" width = "100%"></iframe>';
die();
}
include ('../config/main.inc.php');
echo "<style>
#box {
border: 1px solid #999999;
width:615px;
font-family: Arial;
color: #333;
font-size: 11px;
}
.boxtitle
{
  height: 12px !important;
  padding: 2px 10px 5px 5px;
  border-bottom: 1px solid #999;
  font-family: Arial;
  color: #333;
  font-size: 11px;
  font-weight: bold;
  overflow: hidden;
  background: url(../skins/crystal/images/listheader.gif) top left repeat-x #CCC;
  width: 600px;
  
}
.row {
padding: 2px 10px 5px 5px;
margin:0px;
border-bottom: 1px solid #999;
}
</style>
<div id='box'>
<div class='boxtitle'>Admin Email
</tr> 
</thead></div>
";
$end = array_keys($cmail_config['users']);
$end = end($end);
foreach(array_keys($cmail_config['users']) as $user) {
if ($user == $end) {
echo "<ul class='row' style='border:0;'>".$user."
</ul> 
";
}else {
echo "<ul class='row'>".$user."
</ul> 
";
}
}
echo "
</div>
</div>";