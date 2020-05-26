<?php
	require_once("config.php");
	require_once("funcs.php");
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);
	if($con->connect_error)
		die("Kechirasiz JIDDIY texnik nosozlik sodir bo'ldi. Iltimos texnik hodimlarga habar bering.");
?>
<!DOCTYPE html>
<html>
<head>
	<title>UZMIA - Registration</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
	<style type="text/css">
		:root {
			font-size: calc(.4em + .5vw);
		}
		.table td, .table th {
			padding: .3rem;
		}
		.table tbody tr {
			cursor: pointer;
		}
		.table tbody td[colspan] {
			background-color: #f7f7f7;
			border: 0;
			padding: 0;
		}
		.data_name {
			font-size: .8rem;
			color: #999;
			font-weight: bold;
		}
		.hide-error {
			display: none;
		}
	</style>
</head>
<body>
	<div class="container">
		<br/>
		<h2>Telegram responses</h2>
		<p>Seen: <?php echo count_users($con)["COUNT"]; ?> times, Registered: <?php echo count_candidates($con)["COUNT"]; ?> candidates</p>            
		<form method="POST" action="excel.php">
		  <button class="btn btn-success" type="submit" name="export" value="Export Excel File">Export to Excel</button>
		</form>
		<br/>	

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>NAME</th>
					<th>AGE</th>
					<th>REGION</th>
					<th>LEVEL</th>
					<th>PHONE</th>
				</tr>
			</thead>
			<tbody>
				<div class="hide-error">
				<?php
					$result = get_all($con);
					$count = 0;
					while($row = mysqli_fetch_assoc($result)) {
						$count++;
				?>
				</div>
				<tr data-toggle="collapse" data-target="#data<?php echo $count; ?>">
					<td><?php echo $count; ?></td>
					<td><?php echo $row["NAME"]; ?></td>
					<td><?php echo $row["AGE"]; ?></td>
					<td><?php echo $row["REGION"]; ?></td>
					<td><?php echo $row["LEVEL"]; ?></td>
					<td><a href="tel:<?php echo $row['PHONE']; ?>"> <?php echo read_phone($row["PHONE"]); ?> </a></td>
				</tr>
				<!-- COLLAPSE PANEL - TELEGRAM USER INFORMATION -->
				<tr>
					<td colspan="6">
						<div id="data<?php echo $count; ?>" class="collapse">
							<table class="table">
								<thead>
									<tr>
										<th></th>
										<th class="data_name">Username</th>
										<th class="data_name">Registered time</th>
										<th class="data_name">School</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td></td>
										<td><a href="https://t.me/<?php echo $row['USERNAME']; ?>">@<?php echo $row["USERNAME"]; ?></a></td>
										<td><?php echo $row["REGISTERED"]; ?></td>
										<td><?php echo $row["SCHOOL"]; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			<?php } ?>				
			</tbody>
		</table>
	</div>
</body>
</html>
<?php mysqli_close($con); ?>