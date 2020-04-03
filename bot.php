<?php
	include "private.php";

	// DB CONNECTION
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);

	if(!con) {
		die("DATABASE CONNECTION FAILED!");
	}

	//	WEBHOOK VARIABLES
	$update = json_decode(file_get_contents("php://input"));
	
	$user_id		= $update -> message -> from -> id;
	$first_name		= $update -> message -> from -> first_name;
	$username		= $update -> message -> from -> username;

	$chat_id		= $update -> message -> chat -> id;
	$ini_date		= $update -> message -> date;
	$message		= $update -> message -> text;


	//	Functions
	function bot($method, $datas=[]) {
		$url = "https://api.telegram.org/bot" .API_KEY ."/" .$method;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
		$res = curl_exec($ch);
		if(curl_error($ch))
			var_dump(curl_error($ch));
		else
			return json_decode($res);
	}

	function typing($ch) {
		return bot("sendChatAction", [
			"chat_id" => $ch,
			"action" => "typing"
		]);
	}

	function delete_files($target) {
	    if(is_dir($target)) {
	        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

	        foreach( $files as $file ){
	            delete_files( $file );      
	        }

	        rmdir( $target );
	    } elseif(is_file($target)) {
	        unlink( $target );  
	    }
	}

	function upload($name, $level, $phone) {
		$regs = file_get_contents("regs.log");
		$regs++;
		$data = "
		<tr>
			<td>".$regs."</td>
			<td>".$name."</td>
			<td>".$level."</td>
			<td>".$phone."</td>
		</tr>
		";
		file_put_contents("table.html", $data, FILE_APPEND);
		file_put_contents("regs.log", $regs);
	}

	function seen() {
		$num = file_get_contents("seen.log");
		$num++;
		file_put_contents("seen.log", $num);
	}

	// Buttons
	$buttons = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "👥Ro'yxatdan o'tish"]
			],
			[
				["text" => "📍Manzil"],
				["text" => "📲Biz bilan bog'lanish"]
			]
		]
	]);

	$cancel = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "Bekor qilish"]
			]
		]
	]);

	$yes_no = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "Bekor qilish"],
				["text" => "Yuborish"],
			]
		]
	]);

	$level = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "7-8 sinf"],
				["text" => "9-10-11 sinf"]
			],
			[
				["text" => "Bekor qilish"]
			]
		]
	]);

	//	******************* REGISTRATION STARTS FROM HERE ************************
	if(isset($text)) {
		typing($chat_id);
		if($text == "👥Ro'yxatdan o'tish") {
			mkdir($chat_id);
			file_put_contents("$chat_id/step.log", "0");
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Ism sharifingizni kiriting:",
				"parse_mode" => "markdown",
				"reply_markup" => $cancel
			]);
		}

		else if(is_dir($chat_id)) {
			if(file_get_contents("$chat_id/step.log") == "0") {
				if($text == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "1");
					file_put_contents("$chat_id/name.log", $text);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Nechinchi sinf o'quvchisisiz?",
						"parse_mode" => "markdown",
						"reply_markup" => $level
					]);
				}
			}
			else if(file_get_contents("$chat_id/step.log") == "1") {
				if($text == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "2");
					file_put_contents("$chat_id/level.log", $text);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "📞 Telefon raqamingizni kiriting: ",
						"parse_mode" => "markdown",
						"reply_markup" => $cancel
					]);
				}
			}
			else if(file_get_contents("$chat_id/step.log") == "2") {
				if($text == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "3");
					file_put_contents("$chat_id/phone.log", $text);
					$name = file_get_contents("$chat_id/name.log");
					$level = file_get_contents("$chat_id/level.log");
					$phone = file_get_contents("$chat_id/phone.log");
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" =>  "Ma'lumotlarni tasdiqlaysizmi?\n\nIsmingiz: $name\nToifangiz: $level\nTelefon Raqamingiz: $phone\n",
						"parse_mode" => "markdown",
						"reply_markup" => $yes_no
					]);
				}
			}
			else if(file_get_contents("$chat_id/step.log") == "3") {
				if($text == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else if($text == "Yuborish") {
					$name = file_get_contents("$chat_id/name.log");
					$level = file_get_contents("$chat_id/level.log");
					$phone = file_get_contents("$chat_id/phone.log");
					upload($name, $level, $phone);
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "✅ Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				}
			}
		}
		//	********************************************************************
		else if($text == "/start") {
			seen();
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Assalomu Alaykum!",
				"parse_mode" => "markdown",
				"reply_markup" => $buttons
			]);
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "<b>\"Muhammad al-Xorazmiy\"</b> nomidagi axborot texnologiyalari va <b>\"O'zbekiston Matematiklar va Informatika Assotsiatsiyasi\"</b> tashabbusi bilan birga tashkil etilgan matematika olimpiadasi qabuliga xush kelibsiz!",
				"parse_mode" => "html",
				"reply_markup" => $buttons
			]);
		}

		else if($text == "📍Manzil") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "📌Musobaqa manzili:",
				"parse_mode" => "markdown",
				"reply_markup" => $buttons
			]);
			bot("sendLocation", [
				"chat_id" => $chat_id,
				"latitude" => "41.302632",
				"longitude" => "69.315566",
				"reply_markup" => $buttons
			]);
		}

		else if($text == "📲Biz bilan bog'lanish") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "📥 <b>Biz bilan bog'lanish:</b> \n\n📞 Tel.: <a href=\"tel:998712670027\">+998 71-267-00-27</a>\n🌐 Telegram: <a href=\"https://t.me/uzmia31\">UZMIA</a>",
				"parse_mode" => "html",
				"reply_markup" => $buttons
			]);
		}

		else if($text == "Bekor qilish") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Ro'yxatdan o'tish bekor qilindi!",
				"parse_mode" => "markdown",
				"reply_markup" => $buttons
			]);
		}

		else {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Iltimos, quyidagi tugmalardan birini bosing!",
				"parse_mode" => "markdown",
				"reply_markup" => $buttons
			]);
		}
	}
?>