<?php
	$output = file_get_contents("table.html");
	if(isset($_POST["export"])) {
		header("Content-Type:application/xls");
		header("Content-Disposition:attachment;filename=\"candidates.xls\"");
		$output .= "</table>";
		echo $output;
		die();
	} echo "error accured!";
?>