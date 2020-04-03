<!DOCTYPE html>
<html>
<head>
	<title>UZMIA - Registration</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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