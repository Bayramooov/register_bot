<?php
	require_once("config.php");
	require_once("funcs.php");

	file_get_contents(DELETE_WEBHOOK);

	$update = json_decode(file_get_contents(GET_UPDATES), true);
	
	while (true) {
		$update_id = end($update['result']);
		if (!isset($update_id['update_id']))
			break;
		$update_id = $update_id['update_id'];
		$update_id++;
		$update = offset($update_id);
		sleep(0.1);
	}

	$update = json_decode(file_get_contents(SET_WEBHOOK), true);
	print_r($update);
	echo "<br><br>Success";
?>