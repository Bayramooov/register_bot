<?php
	require_once("settings.php");
	require_once("funcs.php");
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);
	if($con->connect_error)
		die("Kechirasiz JIDDIY texnik nosozlik sodir bo'ldi. Iltimos texnik hodimlarga habar bering.");

	// REGION
	$data1 = group_region($con);
	$region = array();
	$region["Toshkent shahri"] = 0;
	$region["Toshkent viloyati"] = 0;
	$region["Andijon"] = 0;
	$region["Buxoro"] = 0;
	$region["Jizzax"] = 0;
	$region["Farg'ona"] = 0;
	$region["Qashqadaryo"] = 0;
	$region["Xorazm"] = 0;
	$region["Namangan"] = 0;
	$region["Navoiy"] = 0;
	$region["Samarqand"] = 0;
	$region["Surxondaryo"] = 0;
	$region["Sirdaryo"] = 0;
	$region["Qoraqalpog'iston"] = 0;
	while($value = mysqli_fetch_assoc($data1)) {
		$region[$value['REGION']] = $value['NUMBER'];
	}

	// LEVEL
	$data2 = group_level($con);
	$level = array();
	$level["9 - sinf"] = 0;
	$level["10 - sinf"] = 0;
	$level["11 - sinf"] = 0;
	$level["Talaba"] = 0;
	while($value = mysqli_fetch_assoc($data2)) {
		$level[$value['LEVEL']] = $value['NUMBER'];
	}
	// AGE
	$data3 = group_age($con);
	$age = array();
	while($value = mysqli_fetch_assoc($data3)) {
		$age[$value['AGE']] = $value['NUMBER'];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Uzmia | Dashboard</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css">
	<style type="text/css">
		:root {
			font-size: calc(.4em + .5vw);
		}
		.hide-error {
			display: none;
		}
		.chart-container {
			display: flex;
			justify-content: space-around;
			align-items: center;
			align-content: space-around;
			flex-wrap: wrap;
			margin: 0 auto;
		}
	</style>
</head>
<body>
	<div class="container">
		<br/>
		<h2>Uzmia Statistics</h2>
		<p>Total: <?php echo count_users($con)["COUNT"]; ?> users, Registered: <?php echo count_candidates($con)["COUNT"]; ?> candidates, Not Registered: <?php echo count_non_reg($con)["COUNT"]; ?> viewers</p>            
		<form method="POST" action="excel.php">
		  <button class="btn btn-success" type="submit" name="export" value="Export Excel File">Export to Excel</button>
		  <a class="btn btn-success" href="table.php">View Table</a>
		</form>
		<br/>
		<br/>
		<div class="chart-container">
			<canvas id="region" height="500" width="450px"></canvas>
			<canvas id="level" height="450" width="400px"></canvas>
			<canvas id="age" height="500" width="600px"></canvas>
			<!-- <canvas id="time" height="500" width="600px"></canvas> -->
		</div>
	</div>
	<!-- CHART JS -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
	<script type="text/javascript">
		var level_data = {
		  labels: [
		  	"9-sinf",
		  	"10-sinf",
		  	"11-sinf",
		  	"Talaba"
		  ],
		  datasets: [{
		    data: [
		    	<?php echo $level["9 - sinf"] ?>,
		    	<?php echo $level["10 - sinf"] ?>,
		    	<?php echo $level["11 - sinf"] ?>,
		    	<?php echo $level["Talaba"] ?>
		    ],
		    backgroundColor: [
		    	"rgba(5,47,95,0.4)",
		    	"rgba(6,167,125,0.4)",
		    	"rgba(99,105,209,0.4)",
		    	"rgba(241,162,8,0.4)"
		    ],
		    borderColor: [
		    	"rgba(5,47,95,1)",
		    	"rgba(6,167,125,1)",
		    	"rgba(99,105,209,1)",
		    	"rgba(241,162,8,1)"
		    ],
		    borderWidth: 2,
		    hoverBackgroundColor: [
		    	"rgba(0,255,0,0.4)",
		    	"rgba(0,255,0,0.4)",
		    	"rgba(0,255,0,0.4)",
		    	"rgba(0,255,0,0.4)",
			],
		    hoverBorderColor: "rgba(0,255,0,1)",
		  }]
		};

		var level_option = {
		  responsive: false,
		  responsiveAnimationDuration: 750,
		  maintainAspectRatio: true,
		};

		var region_data = {
		  labels: [
		  	"Toshkent shahri",
		  	"Toshkent viloyati",
		  	"Andijon",
		  	"Buxoro",
		  	"Jizzax",
		  	"Farg'ona", 
		  	"Qashqadaryo",
		  	"Xorazm",
		  	"Namangan",
		  	"Navoiy",
		  	"Samarqand",
		  	"Surxondaryo",
		  	"Sirdaryo",
		  	"Qoraqalpog'iston"
		  ],
		  datasets: [{
		    label: "Viloyatlar",
		    backgroundColor: "rgba(65,164,10,0.4)",
		    borderColor: "rgba(76,169,8,1)",
		    borderWidth: 2,
		    hoverBackgroundColor: "rgba(0,255,0,0.4)",
		    hoverBorderColor: "rgba(0,255,0,1)",
		    data: [
		    	<?php echo $region["Toshkent shahri"] ?>,
		    	<?php echo $region["Toshkent viloyati"] ?>,
		    	<?php echo $region["Andijon"] ?>,
		    	<?php echo $region["Buxoro"] ?>,
		    	<?php echo $region["Jizzax"] ?>,
		    	<?php echo $region["Farg'ona"] ?>,
		    	<?php echo $region["Qashqadaryo"] ?>,
		    	<?php echo $region["Xorazm"] ?>,
		    	<?php echo $region["Namangan"] ?>,
		    	<?php echo $region["Navoiy"] ?>,
		    	<?php echo $region["Samarqand"] ?>,
		    	<?php echo $region["Surxondaryo"] ?>,
		    	<?php echo $region["Sirdaryo"] ?>,
		    	<?php echo $region["Qoraqalpog'iston"] ?>
		    ],
		  }]
		};

		var region_option = {
		  responsive: false,
		  responsiveAnimationDuration: 750,
		  maintainAspectRatio: true,
		  scales: {
		    yAxes: [{
		      stacked: true,
		      gridLines: {
		        display: true,
		        color: "rgba(255,99,132,0.2)"
		      }
		    }],
		    xAxes: [{
		      gridLines: {
		        display: false
		      }
		    }]
		  }
		};

		var age_data = {
		  labels: [
		  	<?php
		  		foreach ($age as $key => $value) {
		  			echo $key .",";
		  		}
		  	?>
		  ],

		  datasets: [{
		    data: [
		  	<?php
		  		foreach ($age as $key => $value) {
		  			echo $value .",";
		  		}
		  	?>
		    ],

		    label: "Ishtirokchilarning Yoshlari",
		    fill: true,
		    lineTension: .5,
		    pointRadius: 3,
		    showLine: true,

		    backgroundColor: [
		    	"rgba(255,99,0,0.2)",
		    ],
		    borderColor: [
		    	"rgba(255,99,0,.5)"
		    ],
		    borderWidth: 2,
		    hoverBackgroundColor: [
		    	"rgba(255,99,132,0.4)",
		    	"rgba(255,99,132,0.4)",
		    	"rgba(255,99,132,0.4)",
		    	"rgba(255,99,132,0.4)"
			],
		    hoverBorderColor: "rgba(255,99,132,10)",
		  }]
		};

		var age_option = {
		  responsive: false,
		  responsiveAnimationDuration: 750,
		  maintainAspectRatio: true,
		  scales: {
		    yAxes: [{
		      stacked: true,
		      gridLines: {
		        display: true,
		        color: "rgba(255,99,132,0.2)"
		      }
		    }],
		    xAxes: [{
		      gridLines: {
		        display: false
		      }
		    }]
		  }
		};

		// new Chart('time', {type: 'doughnut', options: time_option,  data: time_data} );
		new Chart('region', {type: 'bar', options: region_option,  data: region_data} );
		new Chart('level', {type: 'doughnut', options: level_option,  data: level_data} );
		new Chart('age', {type: 'line', options: age_option,  data: age_data} );
	</script>
</body>
</html>
<?php mysqli_close($con); ?>