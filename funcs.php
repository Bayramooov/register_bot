<?php
	require_once("private.php");
	function add_user($con, $update) {
		$chat_id	= $update -> message -> chat -> id;
		$first_name	= $update -> message -> from -> first_name;
		$username	= $update -> message -> chat -> username;
		$ini_date	= $update -> message -> date;
		$query		= "SELECT ID FROM USERS WHERE ID = '$chat_id'";
		$result		= mysqli_query($con, $query);
		$result		= mysqli_fetch_assoc($result);
		if(empty($result)) {
			$query	= "INSERT INTO USERS (CHAT_ID, FIRST_NAME, USERNAME, DATE) VALUES ('$chat_id', '$first_name', '$username', '$ini_date')";
			$result	= mysqli_query($con, $query);
			if(!result)
				return false;	//ERROR!
			return true;		//ADDED
		}
		return true;			//ALREADY HAVE
	}
	function add_candidate($con, $chat_id, $date) {
		$name	= file_get_contents("temp/$chat_id/name.log");
		$age	= file_get_contents("temp/$chat_id/age.log");
		$region	= file_get_contents("temp/$chat_id/region.log");
		$school	= file_get_contents("temp/$chat_id/school.log");
		$level	= file_get_contents("temp/$chat_id/level.log");
		$phone	= file_get_contents("temp/$chat_id/phone.log");
		delete_files("temp/$chat_id");
		$query	= "SELECT * FROM CANDIDATES WHERE NAME = '$name' AND AGE = '$age' AND REGION = '$region' AND LEVEL = '$level' AND CHAT_ID = '$chat_id'";
		$result	= mysqli_query($con, $query);
		$result	= mysqli_fetch_assoc($result);
		if(empty($result)) {
			$query	= "INSERT INTO CANDIDATES (ID, NAME, AGE, REGION, SCHOOL, LEVEL, PHONE, REG_DATE, CHAT_ID) VALUES ('0', '$name', '$age', '$region', '$school', '$level', '$phone', '$date', '$chat_id')";
			$result	= mysqli_query($con, $query);
			if(!result)
				return '0';	//ERROR!
			return '2';		//ADDED
		}
		return '1';			//ALREADY HAVE
	}
	function date_convert($date) {
		$time = date("d-m-Y H:i:s", $date);
		return $time;
	}
	function delete_files($location) {
	    if(is_dir($location)) {
	    	// GLOB_MARK adds a slash to directories returned
	        $files = glob($location .'*', GLOB_MARK);
	        foreach($files as $file)
	            delete_files($file);
	        rmdir($location);
	    }
	    else if(is_file($location))
			unlink($location);  
	}
	function bot($method, $datas=[]) {
		$url = URL .API_KEY ."/" .$method;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		$result = curl_exec($ch);
		if(curl_error($ch))
			var_dump(curl_error($ch));
		else
			return json_decode($result);
	}
	function typing($chat_id) {
		return bot("sendChatAction", [
			"chat_id" => $chat_id,
			"action" => "typing"
		]);
	}
?>