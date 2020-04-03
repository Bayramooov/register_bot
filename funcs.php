<?php
	require_once("private.php");
	require_once("bot.php");

	GLOBAL $con;
	GLOBAL $update;
	GLOBAL $user_id;
	GLOBAL $first_name;
	GLOBAL $username;
	GLOBAL $chat_id;
	GLOBAL $ini_date;
	GLOBAL $message;


	function add_user() {
		$query = "INSERT INTO USERS VALUES ($user_id, $chat_id, $first_name, $username, $ini_date)";
		$result = msqli_query($con, $query);
		if(!result)
			echo "add_user() - error!";
	}

	function delete_files() {
	    if(is_dir("temp/$chat_id")) {

	    	//GLOB_MARK adds a slash to directories returned
	        $files = glob("temp/$chat_id" .'*', GLOB_MARK);

	        foreach($files as $file)
	            delete_files($file);

	        rmdir("temp/$chat_id");

	    }
	    else if(is_file("temp/$chat_id"))
			unlink("temp/$chat_id");  
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
			return json_decode($res);
	}

	function typing() {
		return bot("sendChatAction", [
			"chat_id" => $chat_id,
			"action" => "typing"
		]);
	}

?>