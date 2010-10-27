<form action="index.php?_step=3" method="post">

<?php

		$content = $_SESSION['main.inc.php'];
		$new_main = '../config/main.inc.php';
		$handle_main = fopen($new_main, 'x+') or die("<div class='fail'>Error! Cannot write to files to server please chmod the entire Crystal directory 775. Thank-you!</div>");
		fwrite ($handle_main,$content);
		fclose($handle_main);
	
		$handle_db = fopen('../config/db.inc.php', 'x+') or die();
		fwrite ($handle_db,$_SESSION['db.inc.php']);
		fclose($handle_db);
	
 	
?>
<div id="rounded" class="rounded"><center>
	<div id="installing">
	<h3>Installing</h3>
	<div id="loading"><img src="images/loading.gif" id="loading.gif" name="loading.gif" alt="loading.gif" /></div>
</div>
<?php

	//Delay, then load next page
    echo '<div id="complete" style="display:none"><h3 class="success">Install Complete!</h3></div>';
    echo "<script>setTimeout(function() { $('#installing').fadeOut(); }, 2000);</script>";
    echo "<script>setTimeout(function() { $('#complete').fadeIn(); }, 2500);</script>";
	if (!isset($_GET['reload'])) {
		echo '<meta http-equiv=Refresh content="4;url=index.php?_step=4">';
	}
	
?>
</center>
</div>
</div>
