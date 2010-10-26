// Register a templates definition set named "default".
CKEDITOR.addTemplates( 'default',
{
	// The name of sub folder which hold the shortcut preview images of the templates.
	imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),

	// The templates definitions.
	templates :
		[
			{
				title: 'Happy Birthday',
				image: 'template1.gif',
				description: 'A Simple Happy Birthday E-Card',
				html:
					'<center><table><tr><td width="600px" bgcolor="#8FD8D8"><center><div style="font-family: verdana; border-style:dotted;"><p>Happy Birthday!</p></div></center></td></tr>' +
					'<tr><td><center><p style="font-family:Arial,Helvetica,sans-serif;">I’m wishing you another year<br>Of laughter, joy and fun,<br>Surprises, love and happiness,<br>And when your birthday’s done,<br>I hope you feel deep in your heart,<br>As your birthdays come and go,<br>How very much you mean to me,<br>More than you can know.<br><br><i>By Joanna Fuchs</i></p></center></td></tr>' +
					'<tr><td width="600px" bgcolor="#8FD8D8"><center><p style="font-family: verdana;"><font size="2">Sent From Crystal Mail</font></p></center></td></tr>'
			},
		]
});