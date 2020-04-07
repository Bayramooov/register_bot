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

	$table = "<table>";
	$table .= candidates($con);

	if(isset($_POST["export"])) {
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment;filename=\"candidates.xls\"");
		$table .= "</table>";
		echo $table;
		die();
	} echo "error accured!";
?>