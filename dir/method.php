<?php
	require_once("../private.php");
	require_once("../funcs.php");
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);
	if($con->connect_error)
		die("Kechirasiz JIDDIY texnik nosozlik sodir bo'ldi. Iltimos texnik hodimlarga habar bering.");

	if(isset($_POST["submit"])) {
		$message = $_POST["message"];
		$chat_id = array();
		$id = 0;
		$get = 0;
		for($i=0; $i<10000; $i++) { 
			if(isset($_POST["chat_id$get"])) {
				$chat_id[$id] = $_POST["chat_id$get"];
				$id++;
			}
			$get++;
		}
		foreach ($chat_id as $value) {
			$result = array();
			$request = URL .API_KEY ."/sendMessage?chat_id=" .$value ."&text=" .$message ."&parse_mode=html";
			$result = json_decode(file_get_contents($request), true);
			sleep(0.2);
			if($result['ok'] == true) {
?>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 455.431 455.431" style="width: 18px; enable-background:new 0 0 455.431 455.431;" xml:space="preserve"><path style="fill:#8DC640;" d="M405.493,412.764c-69.689,56.889-287.289,56.889-355.556,0c-69.689-56.889-62.578-300.089,0-364.089s292.978-64,355.556,0S475.182,355.876,405.493,412.764z"/><g style="opacity:0.2;"><path style="fill:#FFFFFF;" d="M229.138,313.209c-62.578,49.778-132.267,75.378-197.689,76.8c-48.356-82.489-38.4-283.022,18.489-341.333c51.2-52.622,211.911-62.578,304.356-29.867C377.049,112.676,330.116,232.142,229.138,313.209z"/></g><path style="fill:#FFFFFF;" d="M195.004,354.453c-9.956,0-19.911-4.267-25.6-12.8l-79.644-102.4c-11.378-14.222-8.533-34.133,5.689-45.511s34.133-8.533,45.511,5.689l54.044,69.689l119.467-155.022c11.378-14.222,31.289-17.067,45.511-5.689s17.067,31.289,5.689,45.511L220.604,341.653C213.493,348.764,204.96,354.453,195.004,354.453z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
<?php
				echo $value ."<br>";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>UZMIA - Users</title>
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
		.hide-error {
			display: none;
		}
	</style>
</head>
<body>
	<div class="container">
		<br/>
		<h2>Sending messages</h2>
		<p>Seen: <?php echo count_users($con)["COUNT"]; ?> times, Registered: <?php echo count_candidates($con)["COUNT"]; ?> candidates</p>            
		
		<form method="post">
			<input class="btn btn-success" type="submit" name="submit" value="Send Message" role="button"/>
			<br>
			<br>
			<textarea class="form-control" name="message" rows="4" placeholder="Write a message..."></textarea>
			<br/>	
			<h5>Registered Candidates</h5>
			<table class="table table-hover">
				<thead>
					<tr>
						<th><input type="checkbox" id="select-all1" name="select-all"/></th>
						<th></th>
						<th>NAME</th>
						<th>USERNAME</th>
						<th>REGISTERED</th>
						<th>CHAT_ID</th>
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
					<tr data-toggle="collapse">
						<td><input type="checkbox" id="chat_id1" class="chat_id1" <?php $id=$count-1; echo " name='chat_id$id' "; echo " value='{$row["CHAT_ID"]}' "; ?> /></td>
						<td><?php echo $count; ?></td>
						<td><?php echo $row["NAME"]; ?></td>
						<td><a href="https://t.me/<?php echo $row['USERNAME']; ?>">@<?php echo $row["USERNAME"]; ?></a></td>
						<td><?php echo $row["REGISTERED"]; ?></td>
						<td><?php echo $row["CHAT_ID"]; ?></td>
					</tr>
					<?php } ?>				
				</tbody>
			</table>

			<br/>	
			<br/>	
			<h5>Not Registered Viewers</h5>
			<table class="table table-hover">
				<thead>
					<tr>
						<th><input type="checkbox" id="select-all2" name="select-all"/></th>
						<th></th>
						<th>TG NAME</th>
						<th>USERNAME</th>
						<th>VIEW TIME</th>
						<th>CHAT ID</th>
					</tr>
				</thead>
				<tbody>
					<div class="hide-error">
					<?php
						$result2 = not_registered_viewers($con);
						$count2 = 0;
						while($row2 = mysqli_fetch_assoc($result2)) {
							$count2++;
							$count++;
					?>
					</div>
					<tr>
						<td><input type="checkbox" id="chat_id2" class="chat_id2" <?php $id=$count-1; echo " name='chat_id$id' "; echo " value='{$row2["CHAT_ID"]}' "; ?> /></td>
						<td><?php echo $count2; ?></td>
						<td><?php echo $row2["TELEGRAM_NAME"]; ?></td>
						<td><a href="https://t.me/<?php echo $row2['USERNAME']; ?>">@<?php echo $row2["USERNAME"]; ?></a></td>
						<td><?php echo $row2["VIEWED"]; ?></td>
						<td><?php echo $row2["CHAT_ID"]; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</form>	
	</div>
	<script type="text/javascript">
		document.getElementById('select-all1').onclick = function() {
		  var checkboxes = document.getElementsByClassName('chat_id1');
		  for (var checkbox of checkboxes) {
		    checkbox.checked = this.checked;
		  }
		}
		document.getElementById('select-all2').onclick = function() {
		  var checkboxes = document.getElementsByClassName('chat_id2');
		  for (var checkbox of checkboxes) {
		    checkbox.checked = this.checked;
		  }
		}
	</script>
</body>
</html>