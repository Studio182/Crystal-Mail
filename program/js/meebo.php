<?php
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
if (typeof Meebo == 'undefined') {
	Meebo=function(){(Meebo._=Meebo._||[]).push(arguments)};
	(function(_){var d=document,b=d.body,c;if(!b){c=arguments.callee;
	return setTimeout(function(){c(_)},100)}var a='appendChild',c='createElement',
	m=b.insertBefore(d[c]('div'),b.firstChild),n=m[a](d[c]('m')),i=d[c]('iframe');
	m.style.display='none';m.id='meebo';i.frameBorder="0";n[a](i).id="meebo-iframe";
	function s(){return['<body onload=\'var d=document;d.getElementsByTagName("head")[0].',
	a,'(d.',c,'("script")).src="http',_.https?'s':'','://',_.stage?'stage-':'',
	'cim.meebo.com','/cim?iv=2&network=',_.network,_.lang?'&lang='+_.lang:'',
	_.d?'&domain='+_.d:'','"\'></bo','dy>'].join('')}try{
	d=i.contentWindow.document.open();d.write(s());d.close()}catch(e){
	_.d=d.domain;i.src='javascript:d=document.open();d.write("'+s().replace(/"/g,'\\"')+'");d.close();'}})
    ({ network: '<?php echo$cmail_config['meebo_code'];?>', stage: false });
}
