<HTML>
<HEAD>
<TITLE>Crystal Mail Admin</TITLE>
<SCRIPT type="text/javascript" src="../program/js/jquery-1.4.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="../program/js/jquery.layout.js"></SCRIPT>
<SCRIPT type="text/javascript">
$(document).ready(function () {
    $('body').layout({ applyDefaultStyles: true });
});
</SCRIPT>
<style>

body {
margin: 0;
font-family: "Lucida Grande", Verdana, Arial, Helvetica, sans-serif;
font-size: 10px;
color: #000000;

}
.ui-layout-west, .ui-layout-center{
border: 1px solid #BBB;
margin: 10px;
}
#stat-bar{
margin: 0;
color: #555;
padding-top: 1px;
font-size: 13px;
}
right {
float: right;
padding-right: 5px;
padding-top: 2px;
}
#header {
background-image: url('../skins/crystal/images/header.png');
background-repeat: repeat-x;
margin: 20px 0 0px 0;
padding: 30px;
}
table.records-table thead tr td
{
  height: 20px;
  padding: 0px 4px 0px 4px;
  vertical-align: middle;
  border-bottom: 1px solid #999999;
  color: #333333;
  background: url(../skins/crystal/images/listheader.gif) top left repeat-x #CCC;
  font-size: 10px;
  font-weight: bold;

}

table.records-table tbody tr td
{
  height: 16px;
  padding: 4px 4px 4px 4px;
  font-size: 12px;
  white-space: nowrap;
  border-bottom: 1px solid #EBEBEB;
  overflow: hidden;
  text-align: left;  

}

table.records-table tr
{
  background-color: #FFFFFF;
}

table.records-table tr.selected td
{
  color: #FFFFFF;
  background-color: #CC3333;
}

table.records-table tr.focused td
{
}

table.records-table tr.unfocused td
{
  color: #FFFFFF;
  background-color: #929292;
}
.section{
width: 190px;
}
a {
text-decoration: none;
color: #111;
}
#dashboard-tab
{
position: absolute;
top: 23px;
left: 200px;

}
#plugins-tab
{
position: absolute;
top: 23px;
left: 300px;

}
.tablink {
padding-top: 5px;
padding-left: 5px;
  float: left;
  width: 95px;
  height: 24px !important;
  height: 22px;
  overflow: hidden;
  font-size: 11px;
  overflow: none;
  background: url(../skins/crystal/images/tabs.gif) top left no-repeat;

}
.tablink-active {
padding-top: 5px;
padding-left: 5px;
  float: left;
  width: 95px;
  height: 24px !important;
  height: 22px;
  overflow: hidden;
  font-size: 11px;
  overflow: none;
  background: url(../skins/crystal/images/tabs.gif) top left no-repeat;
  background-position: -102px 0;
}
.tablink-disabled {
padding-top: 5px;
padding-left: 5px;
  float: left;
  width: 95px;
  height: 24px !important;
  height: 22px;
  overflow: hidden;
  font-size: 11px;
  overflow: none;
  background: url(../skins/crystal/images/tabs.gif) top left no-repeat;
  opacity:0.6;
  filter:alpha(opacity=60);
  zoom: 1
}
#intro_text {
color: #444;
}
</style>
<script type="text/javascript" src="js/ajax.js"></script> 
<link rel="shortcut icon" href="../skins/crystal/images/favicon.ico"/>
</HEAD>
<BODY>
<div id="stat-bar">&nbsp;&nbsp;Crystal Mail &nbsp;<small>Admin Panel</small><right><small>Welcome <?php name() ?> | Crystal Version: <strong><?php version() ?></strong> | <a href="?_action=logout">Logout</a></small></right></div>
<div id="header" class="ui-layout-north"><div id="tabsbar">
<?php tabs() ?>

<script type="text/javascript"> if (window.cmail) cmail.add_onload(crystal_init_settings_tabs); </script>
</div></div>
<div id="prefs-box" class="ui-layout-center"><br><br><br><br><br><center><div id="intro_text"><h1>Welcome to the Crystal Webmail Admin Panel<?php finish_it() ?></h1><br><h3>Click on one of the navigation items to get started</h3></div></center>
</div>

<DIV class="ui-layout-west"><table summary="" border="0" id="sections-table" class="records-table" cellspacing="0"><thead><tr><td class="section">Section</td> 
</tr> 
</thead> 
<tbody>
<?php nav() ?>
</tbody> 
</table> </DIV>
</BODY>
</HTML>
