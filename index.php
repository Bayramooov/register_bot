<!DOCTYPE html>
<html>
<head>
	<title>UZMIA - Registration</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap-grid.min.css"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap-reboot.min.css"/>
</head>
<body>
	<div class="container">
		<br/>
		<h2>Telegram responses</h2>
		<p>Seen: <?php echo file_get_contents("seen.log"); ?> times, Registered: <?php echo file_get_contents("regs.log"); ?> candidates</p>            
		
		<form method="POST" action="excel.php">
		  <button class="btn btn-success" type="submit" name="export" value="Export Excel File">Export Excel File</button>
		</form>
		
		<br/>

		<?php echo file_get_contents("table.html"); ?>
		</table>
	</div>
</body>
</html>