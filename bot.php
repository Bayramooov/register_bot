<?php
	require_once("private.php");
	/********************************************
		#PREDEFINED CONSTANTS IN PRIVATE.PHP
			DB_HOST
			DB_USERNAME
			DB_PASSWORD
			DB_NAME
			API_KEY
	********************************************/

	// DB CONNECTION
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);

	if(!con)
		die("DATABASE CONNECTION FAILED!");


	//	WEBHOOK
	$update = json_decode(file_get_contents("php://input"));

	$user_id		= $update -> message -> from -> id;
	$first_name		= $update -> message -> from -> first_name;
	$username		= $update -> message -> from -> username;
	$chat_id		= $update -> message -> chat -> id;
	$ini_date		= $update -> message -> date;
	$message		= $update -> message -> text;


	// VARIABLES
	$name = "";
	$age = "";
	$city = "";
	$address = "";
	$school = "";
	$level = "";

	// ADDING USER TO USER TABLE
	add_user();


	// TELEGRAM BUTTONS
	$default = json_encode([
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

	$submit = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "Tasdiqlash"],
				["text" => "Bekor qilish"],
			]
		]
	]);

	$level = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "9 - sinf"],
				["text" => "10 - sinf"],
				["text" => "11 - sinf"]
			],
			[
				["text" => "Talaba"],
			],
			[
				["text" => "Bekor qilish"]
			]
		]
	]);

	//	******************* REGISTRATION STARTS FROM HERE ************************
	if(isset($message)) {
		typing();

		if($message == "👥Ro'yxatdan o'tish") {
			mkdir("temp/$chat_id");
			file_put_contents("temp/$chat_id/step.log", "0");
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Ism sharifingizni kiriting:",
				"parse_mode" => "markdown",
				"reply_markup" => $cancel
			]);
		}

		else if(is_dir("temp/$chat_id")) {
			$step = file_get_contents("$chat_id/step.log");
			if($step == "0") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "1");
					file_put_contents("$chat_id/name.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Nechinchi sinf o'quvchisisiz?",
						"parse_mode" => "markdown",
						"reply_markup" => $level
					]);
				}
			}
			else if(file_get_contents("$chat_id/step.log") == "1") {
				if($message == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "2");
					file_put_contents("$chat_id/level.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "📞 Telefon raqamingizni kiriting: ",
						"parse_mode" => "markdown",
						"reply_markup" => $cancel
					]);
				}
			}
			else if(file_get_contents("$chat_id/step.log") == "2") {
				if($message == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else {
					file_put_contents("$chat_id/step.log", "3");
					file_put_contents("$chat_id/phone.log", $message);
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
				if($message == "Bekor qilish") {
					delete_files($chat_id);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $buttons
					]);
				} else if($message == "Yuborish") {
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
		else if($message == "/start") {
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

		else if($message == "📍Manzil") {
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

		else if($message == "📲Biz bilan bog'lanish") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "📥 <b>Biz bilan bog'lanish:</b> \n\n📞 Tel.: <a href=\"tel:998712670027\">+998 71-267-00-27</a>\n🌐 Telegram: <a href=\"https://t.me/uzmia31\">UZMIA</a>",
				"parse_mode" => "html",
				"reply_markup" => $buttons
			]);
		}

		else if($message == "Bekor qilish") {
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