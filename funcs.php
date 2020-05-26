<?php
	require_once("config.php");
	function add_user($con, $update) {
		$chat_id	= $update -> message -> chat -> id;
		$first_name	= $update -> message -> from -> first_name;
		$first_name = mysqli_real_escape_string($con, $first_name);
		$username	= $update -> message -> chat -> username;
		$username	= mysqli_real_escape_string($con, $username);
		$date		= $update -> message -> date;
		$date		= date_convert($date);
		$query		= "SELECT ID FROM USERS WHERE ID = '$chat_id'";
		$result		= mysqli_query($con, $query);
		$result		= mysqli_fetch_assoc($result);
		if(empty($result)) {
			$query	= "INSERT INTO USERS (CHAT_ID, FIRST_NAME, USERNAME, DATE) VALUES ('$chat_id', '$first_name', '$username', '$date')";
			$result	= mysqli_query($con, $query);
			if(!result)
				return false;	//ERROR!
			return true;		//ADDED
		}
		return true;			//ALREADY HAVE
	}
	function add_candidate($con, $chat_id, $date) {
		$name	= file_get_contents("temp/$chat_id/name.log");
		$name	= mysqli_real_escape_string($con, $name);
		$age	= file_get_contents("temp/$chat_id/age.log");
		$region	= file_get_contents("temp/$chat_id/region.log");
		$region	= mysqli_real_escape_string($con, $region);
		$school	= file_get_contents("temp/$chat_id/school.log");
		$school	= mysqli_real_escape_string($con, $school);;
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
	function count_users($con) {
		$query = "SELECT COUNT(CHAT_ID) AS COUNT FROM USERS";
		$result = mysqli_query($con, $query);
		$result = mysqli_fetch_assoc($result);
		return $result;
	}
	function count_candidates($con) {
		$query = "SELECT COUNT(CHAT_ID) AS COUNT FROM CANDIDATES";
		$result = mysqli_query($con, $query);
		$result = mysqli_fetch_assoc($result);
		return $result;
	}
	function candidates($con) {
		$query = "SELECT C.NAME AS NAME, C.AGE AS AGE, C.REGION AS REGION, C.SCHOOL AS SCHOOL, C.LEVEL AS LEVEL, C.PHONE AS PHONE, U.USERNAME AS USERNAME FROM CANDIDATES C JOIN USERS U ON U.CHAT_ID = C.CHAT_ID ORDER BY C.NAME";
		$result = mysqli_query($con, $query);
		$table = "";
		$counter = 1;
		while($row = mysqli_fetch_assoc($result)) {
			$table = $table ."<tr>\n";
			$table = $table ."\t<td>" .$counter ."</td>\n";
			$table = $table ."\t<td>" .$row["NAME"] ."</td>\n";
			$table = $table ."\t<td>" .$row["AGE"] ."</td>\n";
			$table = $table ."\t<td>" .$row["REGION"] ."</td>\n";
			$table = $table ."\t<td>" .$row["SCHOOL"] ."</td>\n";
			$table = $table ."\t<td>" .$row["LEVEL"] ."</td>\n";
			$table = $table ."\t<td>" .$row["PHONE"] ."</td>\n";
			$table = $table ."</tr>\n";
			$counter++;
		}
		return $table;
	}
	function get_all($con) {
		$query = "SELECT C.NAME, C.AGE, C.REGION, C.SCHOOL, C.LEVEL, C.PHONE, U.FIRST_NAME AS TELEGRAM_NAME, U.USERNAME, U.DATE AS VIEWED, C.REG_DATE AS REGISTERED, U.CHAT_ID FROM CANDIDATES C JOIN USERS U ON U.CHAT_ID = C.CHAT_ID ORDER BY REGISTERED DESC";
		$result = mysqli_query($con, $query);
		if($result->num_rows > 0)
			return $result;
	}
	function phone_filter($phone) {
		if(strlen($phone) == 9)
			return "+998" .$phone;
		else if(strlen($phone) == 12)
			if(substr($phone, 0, 3) == "998")
				return "+" .$phone;
	}
	function read_phone($phone) {
		return substr($phone, 0, 4) ." " .substr($phone, 4, 2) ."-" .substr($phone, 6, 3) ."-" .substr($phone, 9, 2) ."-" .substr($phone, 11, 2);
	}
	function not_registered_viewers($con) {
		$query = "SELECT U.FIRST_NAME AS TELEGRAM_NAME, U.USERNAME, U.DATE AS VIEWED, U.CHAT_ID FROM USERS U WHERE U.CHAT_ID NOT IN(SELECT C.CHAT_ID FROM CANDIDATES C) ORDER BY DATE DESC";
		$result = mysqli_query($con, $query);
		if($result->num_rows > 0)
			return $result;
	}
	function count_non_reg($con) {
		$query = "SELECT COUNT(U.CHAT_ID) AS COUNT FROM USERS U WHERE U.CHAT_ID NOT IN(SELECT C.CHAT_ID FROM CANDIDATES C)";
		$result = mysqli_query($con, $query);
		$result = mysqli_fetch_assoc($result);
		return $result;
	}
	function group_region($con) {
		$query = "SELECT COUNT(ID) AS NUMBER, REGION FROM CANDIDATES GROUP BY REGION ORDER BY NUMBER DESC";
		$result = mysqli_query($con, $query);
		// $result = mysqli_fetch_assoc($result);
		return $result;
	}
	function group_level($con) {
		$query = "SELECT COUNT(ID) AS NUMBER, LEVEL FROM CANDIDATES GROUP BY LEVEL ORDER BY NUMBER DESC";
		$result = mysqli_query($con, $query);
		// $result = mysqli_fetch_assoc($result);
		return $result;
	}
	function group_age($con) {
		$query = "SELECT COUNT(ID) AS NUMBER, AGE FROM CANDIDATES GROUP BY AGE ORDER BY AGE";
		$result = mysqli_query($con, $query);
		// $result = mysqli_fetch_assoc($result);
		return $result;
	}

	function offset($offset_value) {
		$update = json_decode(file_get_contents(GET_UPDATES."?offset=".$offset_value), true);
		return $update;
	}
?>