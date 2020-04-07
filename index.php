<?php
	require_once("private.php");
	require_once("funcs.php");
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);
	if(!$con)
		die("Kechirasiz texnik nosozlik sodir bo'ldi. Bu haqda texnik hodimlarga habar jo'natildi. Iltimos birozdan keyin habar oling.");
?>
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
		<p>Seen: <?php echo count_users($con)["COUNT"]; ?> times, Registered: <?php echo count_candidates($con)["COUNT"]; ?> candidates</p>            
		
		<form method="POST" action="excel.php">
		  <button class="btn btn-success" type="submit" name="export" value="Export Excel File">Export Excel File</button>
		</form>
		<br/>		
		<table class="table table-hover">
			<tr>
				<th></th>
				<th>NAME</th>
				<th>AGE</th>
				<th>REGION</th>
				<th>SCHOOL</th>
				<th>LEVEL</th>
				<th>PHONE</th>
				<th>USERNAME</th>
			</tr>
			<?php echo candidates($con); ?>
		</table>

	</div>
</body>
</html>