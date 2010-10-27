<title>Crystal Mail Installer :: Welcome</title>
</center>
<SCRIPT TYPE="text/javascript">
<!--
function dropdown(mySel)
{
var myWin, myVal;
myVal = mySel.options[mySel.selectedIndex].value;
if(myVal)
   {
   if(mySel.form.target)myWin = parent[mySel.form.target];
   else myWin = window;
   if (! myWin) return true;
   myWin.location = myVal;
   }
return false;
}
//-->
</SCRIPT>
<div id="rounded" class="rounded"><h1><center>Welcome to the Crystal Webmail Installer</h1>
<br>
<center>
<p>You are just moments away from installing the best webmail client ever!<br />
The interactive installer will guide you through the entire installation and even write the configuration files. Just sit back, configuration should take less than 10 minutes!</p>
</center> 

<br /><br />

<div id="reqs">
<p class="bold">Basic Requirements for Crystal Webmail:</p>
<ul>
	<li>PHP Version 5.2.0 or greater including
    	<ul>
			<li>PCRE (perl compatible regular expression)</li>
			<li>Session support</li>
			<li>Libiconv (recommended)</li>
			<li>OpenSSL (recommended)</li>
			<li>FileInfo (optional)</li>
			<li>Multibyte/mbstring (optional)</li>
			<li>Mcrypt (optional)</li>
		</ul>
	</li>
	<li>php.ini options:
	    <ul>
	        <li>error_reporting E_ALL &amp; ~E_NOTICE (or lower)</li>
	        <li>file_uploads on (for attachment upload features)</li>
	        <li>session.auto_start needs to be off</li>
	    </ul>
	</li>
	<li>A MySQL or PostgreSQL database engine or the SQLite extension for PHP</li>
	<li>An SMTP server (recommended) or PHP configured for mail delivery</li>
</ul>
</div>
<FORM 
     ACTION="../cgi-bin/redirect.pl" 
     METHOD=POST onSubmit="return dropdown(this.gourl)">
     <div id="button" width="300px" style='	font-family: "Lucida Grande", "Verdana";
	font-size: 12px;
	color: #3F4245;
	line-height: 1.4em;
	text-shadow: #ffffff 0px 1px 3px;
	-webkit-text-shadow: #ffffff 0px 1px 3px;
	-moz-text-shadow: #ffffff 0px 1px 3px;'>

<SELECT NAME="gourl">

<OPTION VALUE="">Please Select Installer Mode

<OPTION VALUE="?_step=1&level=2"                     >Full (Get all the configuration items)
<OPTION VALUE="?_step=1&level=1"                          >Express (Get only the essential configuration items)

</SELECT>&nbsp;|&nbsp;<INPUT TYPE=SUBMIT VALUE="Next">
</div> 

</FORM>
<br>
</div>
