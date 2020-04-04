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

	/******************
	#VARIABLES
		name
		age
		region
		school
		level
		phone
	******************/

	// VALIDATION
	$age_pattern = "/^\d\d$/";
	
	$is_region =
		  ($message == "Toshkent shahri")
		||($message == "Toshkent viloyati")
		||($message == "Andijon")
		||($message == "Buxoro")
		||($message == "Jizzax")
		||($message == "Farg'ona")
		||($message == "Qashqadaryo")
		||($message == "Xorazm")
		||($message == "Namangan")
		||($message == "Navoiy")
		||($message == "Samarqand")
		||($message == "Surxondaryo")
		||($message == "Sirdaryo")
		||($message == "Qoraqalpog'iston");

	$is_level =
		  ($message == "9 - sinf")
		||($message == "10 - sinf")
		||($message == "11 - sinf")
		||($message == "Talaba");

	$phone_pattern = "/.*/";


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

	$regions = json_encode([
		"resize_keyboard" => true,
		"keyboard" =>
		[
			[
				["text" => "Toshkent shahri"],
				["text" => "Toshkent viloyati"]
			],
			[
				["text" => "Andijon"],
				["text" => "Buxoro"],
				["text" => "Jizzax"]
			],
			[
				["text" => "Farg'ona"],
				["text" => "Qashqadaryo"],
				["text" => "Xorazm"]
			],
			[
				["text" => "Namangan"],
				["text" => "Navoiy"],
				["text" => "Samarqand"]
			],
			[
				["text" => "Surxondaryo"],
				["text" => "Sirdaryo"],
				["text" => "Qoraqalpog'iston"]
			],
			[
				["text" => "Bekor qlish"]
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

		// => REGISTER
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

			// => NAME
			if($step == "0") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else {
					file_put_contents("$chat_id/step.log", "1");
					file_put_contents("$chat_id/name.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Yoshingiz nechada?",
						"parse_mode" => "markdown",
						"reply_markup" => $cancel
					]);
				}
			}

			// => AGE
			else if($step == "1") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else if(preg_match($age_pattern, $message)) {
					file_put_contents("temp/$chat_id/step.log", "2");
					file_put_contents("temp/$chat_id/age.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Iltimos Shahringizni tanlang.\n Quyidagi tugmalardan birini bosing.",
						"parse_mode" => "markdown",
						"reply_markup" => $regions
					]);
				} else {
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Noto'g'ri format! \n Iltimos faqat raqamlar bilan yozing!",
						"parse_mode" => "markdown",
						"reply_markup" => $cancel
					]);
				}
			}

			// => REGION
			else if($step == "2") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else if($is_region) {
					file_put_contents("temp/$chat_id/step.log", "3");
					file_put_contents("temp/$chat_id/region.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" =>  "Toifangizni tanlang. \n Quyidagi tugmalardan birini bosing.",
						"parse_mode" => "markdown",
						"reply_markup" => $level
					]);
				} else {
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" =>  "Noto'g'ri format. \n Iltimos quyidagi tugmalardan birini bosing!",
						"parse_mode" => "markdown",
						"reply_markup" => $regions
					]);
				}
			}

			// => LEVEL
			else if($step == "3") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else if($is_level) {
					file_put_contents("temp/$chat_id/step.log", "4");
					file_put_contents("temp/$chat_id/level.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Iltimos o'quv dargohingizning nomini kiriting.",
						"parse_mode" => "markdown",
						"reply_markup" => $cancel
					]);
				} else {
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" =>  "Noto'g'ri format. \n Iltimos quyidagi tugmalardan birini bosing!",
						"parse_mode" => "markdown",
						"reply_markup" => $regions
					]);
				}
			}

			// => SCHOOL
			else if($step == "4") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else {
					file_put_contents("temp/$chat_id/step.log", "5");
					file_put_contents("temp/$chat_id/school.log", $message);
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Telefon raqamingizni kiriting",
						"parse_mode" => "markdown",
						"reply_markup" => $calcel
					]);
				}
			}

			// => PHONE
			else if($step == "5") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else if(preg_match($phone_pattern, $message)) {
					file_put_contents("temp/$chat_id/step.log", "6");
					file_put_contents("temp/$chat_id/phone.log", $message);

					$name = file_get_contents("temp/chat_id/name.log");
					$age = file_get_contents("temp/chat_id/age.log");
					$region = file_get_contents("temp/chat_id/region.log");
					$level = file_get_contents("temp/chat_id/level.log");
					$school = file_get_contents("temp/chat_id/school.log");
					$phone = file_get_contents("temp/chat_id/phone.log")

					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "
							**Malumotlarni Tasdiqlaysizmi?**\n
							1. F.I.Sh:				*$name*\n
							2. Yoshingiz:			*$age*\n
							3. Shahringiz:			*$region*\n
							4. Toifangiz:			*$level*\n
							5. O'quv muassassasi:	*$school*\n
							6. Telefon raqamingiz:	*$phone*\n",
						"parse_mode" => "markdown",
						"reply_markup" => $submit
					]);
				} else {
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Noto'g'ri format. \n Faqat sonlardan foydalanish mumkin!",
						"parse_mode" => "markdown",
						"reply_markup" => $submit
					]);
				}
			}
			// => SUBMIT
			else if($step == "6") {
				if($message == "Bekor qilish") {
					delete_files();
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Ro'yxat bekor qilindi!",
						"parse_mode" => "markdown",
						"reply_markup" => $default
					]);
				} else if($message == "Tasdiqlash") {
					$name = file_get_contents("temp/chat_id/name.log");
					$age = file_get_contents("temp/chat_id/age.log");
					$region = file_get_contents("temp/chat_id/region.log");
					$level = file_get_contents("temp/chat_id/level.log");
					$school = file_get_contents("temp/chat_id/school.log");
					$phone = file_get_contents("temp/chat_id/phone.log")



					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "✅ Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!",
						"parse_mode" => "markdown",
						"reply_markup" => $submit
					]);
				} else {
					bot("sendMessage", [
						"chat_id" => $chat_id,
						"text" => "Iltimos quyidagi tugmalardan birini bosing!",
						"parse_mode" => "markdown",
						"reply_markup" => $submit
					]);
				}
			}
		}
		//	********************************************************************
		else if($message == "/start") {
			// add_user();

			/////////////////////// TESTING PART ///////////////////////
			if(add_user()) {
				bot("sendMessage", [
					"chat_id" => $chat_id,
					"text" => "Successfully Connected with DB.",
					"parse_mode" => "markdown",
					"reply_markup" => $default
				]);
			} else {
				bot("sendMessage", [
					"chat_id" => $chat_id,
					"text" => "add_user() - error!",
					"parse_mode" => "markdown",
					"reply_markup" => $default
				]);
			}
			/////////////////////// TESTING PART ///////////////////////

			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Assalomu Alaykum!",
				"parse_mode" => "markdown",
				"reply_markup" => $default
			]);
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "<b>\"Muhammad al-Xorazmiy\"</b> nomidagi axborot texnologiyalari va <b>\"O'zbekiston Matematiklar va Informatika Assotsiatsiyasi\"</b> tashabbusi bilan birga tashkil etilgan matematika olimpiadasi qabuliga xush kelibsiz!",
				"parse_mode" => "html",
				"reply_markup" => $default
			]);
		}

		else if($message == "📍Manzil") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "📌Musobaqa manzili:",
				"parse_mode" => "markdown",
				"reply_markup" => $default
			]);
			bot("sendLocation", [
				"chat_id" => $chat_id,
				"latitude" => "41.302632",
				"longitude" => "69.315566",
				"reply_markup" => $default
			]);
		}

		else if($message == "📲Biz bilan bog'lanish") {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "📥 <b>Biz bilan bog'lanish:</b> \n\n📞 Tel.: <a href=\"tel:998712670027\">+998 71-267-00-27</a>\n🌐 Telegram: <a href=\"https://t.me/uzmia31\">UZMIA</a>",
				"parse_mode" => "html",
				"reply_markup" => $default
			]);
		}

		else {
			bot("sendMessage", [
				"chat_id" => $chat_id,
				"text" => "Iltimos, quyidagi tugmalardan birini bosing!",
				"parse_mode" => "markdown",
				"reply_markup" => $default
			]);
		}
	}
?>