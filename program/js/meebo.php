<?php
/*
+----------------------------------------------------------------+
| ./program/js/meebo.php                                         |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Crystal Mail Meebo Script                                    |
|                                                                |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
| William Bentley <william@crystalmail.net>                      |
+----------------------------------------------------------------+
*/
header("Content-type: text/javascript");
error_reporting (E_ALL ^ E_NOTICE);
include('../../config/main.inc.php');
//Check if meebo is even set (If it is not Kill PHP to prevent ssl certificate errors)
if (isset($cmail_config['meebo_code'])) {
if (empty($cmail_config['meebo_code'])) {
die();
}
} else {die();}
?>
$(document).ready(function(){
$("#directorylistbox").css("margin-bottom","15px");
$("#addresslist").css("margin-bottom","15px");
$("#contacts-box").css("margin-bottom","15px");
$("#mainscreen").css("margin-bottom","15px");
});
window.Meebo||function(c){function p(){return["<",i,' onload="var d=',g,";d.getElementsByTagName('head')[0].",
j,"(d.",h,"('script')).",k,"='//cim.meebo.com/cim?iv=",a.v,"&",q,"=",c[q],c[l]?
"&"+l+"="+c[l]:"",c[e]?"&"+e+"="+c[e]:"","'\"></",i,">"].join("")}var f=window,
a=f.Meebo=f.Meebo||function(){(a._=a._||[]).push(arguments)},d=document,i="body",
m=d[i],r;if(!m){r=arguments.callee;return setTimeout(function(){r(c)},100)}a.$=
{0:+new Date};a.T=function(u){a.$[u]=new Date-a.$[0]};a.v=4;var j="appendChild",
h="createElement",k="src",l="lang",q="network",e="domain",n=d[h]("div"),v=n[j](d[h]("m")),
b=d[h]("iframe"),g="document",o,s=function(){a.T("load");a("load")};f.addEventListener?
f.addEventListener("load",s,false):f.attachEvent("onload",s);n.style.display="none";
m.insertBefore(n,m.firstChild).id="meebo";b.frameBorder="0";b.id="meebo-iframe";
b.allowTransparency="true";v[j](b);try{b.contentWindow[g].open()}catch(w){c[e]=
d[e];o="javascript:var d="+g+".open();d.domain='"+d.domain+"';";b[k]=o+"void(0);"}try{var t=
b.contentWindow[g];t.write(p());t.close()}catch(x){b[k]=o+'d.write("'+p().replace(/"/g,
'\\"')+'");d.close();'}a.T(1)}({network:"<?php echo$cmail_config['meebo_code'];?>"});
Meebo("makeEverythingSharable");
