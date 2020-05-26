<?php
	require_once("config.php");
	require_once("funcs.php");
	/***********************************************
		#PREDEFINED CONSTANTS FROM PRIVATE.PHP
			DB_HOST
			DB_USERNAME
			DB_PASSWORD
			DB_NAME
			API_KEY
	***********************************************/
	//	WEBHOOK
	$update = json_decode(file_get_contents("php://input"));
	$chat_id		= $update -> message -> chat -> id;
	$first_name		= $update -> message -> from -> first_name;
	$username		= $update -> message -> chat -> username;
	$date			= $update -> message -> date;
	$message		= $update -> message -> text;
	$date			= date_convert($date);

	// DB CONNECTION
	$con = mysqli_connect(
		DB_HOST,
		DB_USERNAME,
		DB_PASSWORD,
		DB_NAME
	);
	//////////////////////////////////////// EXCEPTION HANGLING ////////////////////////////////////////
	if(!$con) {
		typing($chat_id);
		bot("sendMessage",	[
			"chat_id"		=> $chat_id,
			"text"			=> "Kechirasiz texnik nosozlik sodir bo'ldi. Bu haqda texnik hodimlarga habar jo'natildi. Iltimos birozdan keyin habar oling.",
			"parse_mode"	=> "markdown",
			"reply_markup"	=> $default
		]);
		typing("373537481");
		bot("sendMessage",	[
			"chat_id"		=> "373537481",	//ADMIN
			"text"			=> "<b>DATABASE CONNECTION FAILED!</b>\n\nCHAT_ID: <b><i>$chat_id</i></b>\nName: <b><i>$first_name</i></b>\nUsername: <b><i>@$username</i></b>\nTime: <b><i>$date</i></b>\nMessage:\n\n<b><i>$message</i></b>",
			"parse_mode"	=> "html",
			"reply_markup"	=> ""
		]);
		die();
	}
	//////////////////////////////////////// EXCEPTION HANGLING ////////////////////////////////////////
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
				//["text" => "📍Manzil"],
				["text" => "📜Olimpiada shartlari"],
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

	/////////////////////////////////////// REGISTRATION FLOW ///////////////////////////////////////
	if(isset($message)) {
		// => REGISTER
		if($message == "👥Ro'yxatdan o'tish") {
			if(!is_dir("temp/$chat_id"))
				mkdir("temp/$chat_id");	
			file_put_contents("temp/$chat_id/step.log", "0");
			bot("sendMessage",	[
				"chat_id"		=> $chat_id,
				"text"			=> "Ism sharifingizni kiriting:",
				"parse_mode"	=> "markdown",
				"reply_markup"	=> $cancel
			]);
		}
		
		else if(is_dir("temp/$chat_id")) {
			$step = file_get_contents("temp/$chat_id/step.log");
			// => NAME
			if($step == "0") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else {
					file_put_contents("temp/$chat_id/step.log", "1");
					file_put_contents("temp/$chat_id/name.log", $message);
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Yoshingiz nechada?",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $cancel
					]);
				}
			}
			// => AGE
			else if($step == "1") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else if(preg_match($age_pattern, $message)) {
					file_put_contents("temp/$chat_id/step.log", "2");
					file_put_contents("temp/$chat_id/age.log", $message);
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Iltimos Shahringizni tanlang.\nQuyidagi tugmalardan birini bosing.",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $regions
					]);
				} else {
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Noto'g'ri format!\nIltimos faqat raqamlar bilan yozing!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $cancel
					]);
				}
			}
			// => REGION
			else if($step == "2") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else if($is_region) {
					file_put_contents("temp/$chat_id/step.log", "3");
					file_put_contents("temp/$chat_id/region.log", $message);
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Toifangizni tanlang.\nQuyidagi tugmalardan birini bosing.",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $level
					]);
				} else {
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Noto'g'ri format.\nIltimos quyidagi tugmalardan birini bosing!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $regions
					]);
				}
			}
			// => LEVEL
			else if($step == "3") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else if($is_level) {
					file_put_contents("temp/$chat_id/step.log", "4");
					file_put_contents("temp/$chat_id/level.log", $message);
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Iltimos o'quv muassassangizning nomini kiriting.",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $cancel
					]);
				} else {
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Noto'g'ri format.\nIltimos quyidagi tugmalardan birini bosing!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $level
					]);
				}
			}
			// => SCHOOL
			else if($step == "4") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else {
					file_put_contents("temp/$chat_id/step.log", "5");
					file_put_contents("temp/$chat_id/school.log", $message);
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Telefon raqamingizni kiriting",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $cancel
					]);
				}
			}
			// => PHONE
			else if($step == "5") {
				$message = preg_replace("/[^0-9]+/", "", $message);
				$phone = phone_filter($message); 
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else if(isset($phone)) {
					file_put_contents("temp/$chat_id/step.log", "6");
					file_put_contents("temp/$chat_id/phone.log", $phone);
					$name	= file_get_contents("temp/$chat_id/name.log");
					$age	= file_get_contents("temp/$chat_id/age.log");
					$region	= file_get_contents("temp/$chat_id/region.log");
					$level	= file_get_contents("temp/$chat_id/level.log");
					$school	= file_get_contents("temp/$chat_id/school.log");
					$phone	= file_get_contents("temp/$chat_id/phone.log");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "<b>Malumotlarni Tasdiqlaysizmi?</b>\n1. F.I.Sh: <b><i>$name</i></b>\n2. Yoshingiz: <b><i>$age</i></b>\n3. Shahringiz: <b><i>$region</i></b>\n4. Toifangiz: <b><i>$level</i></b>\n5. O'quv muassassasi: <b><i>$school</i></b>\n6. Telefon raqamingiz: <b><i>$phone</i></b>",
						"parse_mode"	=> "html",
						"reply_markup"	=> $submit
					]);
				} else {
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Noto'g'ri format.\nTelefon raqamini to'g'ri kiriting!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $cancel
					]);
				}
			}
			// => SUBMIT
			else if($step == "6") {
				if($message == "Bekor qilish") {
					delete_files("temp/$chat_id");
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Ro'yxat bekor qilindi!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $default
					]);
				} else if($message == "Tasdiqlash") {
					switch (add_candidate($con, $chat_id, $date)) {
						////////////////////////// EXCEPTION HANGLING //////////////////////////
						case '0':
							bot("sendMessage",	[
								"chat_id"		=> $chat_id,
								"text"			=> "Kechirasiz texnik nosozlik sodir bo'ldi va ro'yxat bekor qilindi. Bu haqda texnik hodimlarga habar jo'natildi. Iltimos birozdan keyin qayta ro'yxatdan o'ting.",
								"parse_mode"	=> "markdown",
								"reply_markup"	=> $default
							]);
							bot("sendMessage",	[
								"chat_id"		=> "373537481",	//ADMIN
								"text"			=> "<b>ADD_CANDIDATE(); FAILED!</b>\n\nCHAT_ID: <b><i>$chat_id</i></b>\nName: <b><i>$first_name</i></b>\nUsername: <b><i>@$username</i></b>\nTime: <b><i>$date</i></b>\nMessage:\n\n<b><i>$message</i></b>",
								"parse_mode"	=> "html",
								"reply_markup"	=> ""
							]);
							die();
						////////////////////////// EXCEPTION HANGLING //////////////////////////
						case '1':
							bot("sendMessage",	[
								"chat_id"		=> $chat_id,
								"text"			=> "⚠️ Siz ro'yxatdan o'tib bo'lgansiz!",
								"parse_mode"	=> "markdown",
								"reply_markup"	=> $default
							]);
							break;
						case '2':
							bot("sendMessage",	[
								"chat_id"		=> $chat_id,
								"text"			=> "✅ Ro'yxatdan o'tish muvaffaqiyatli yakunlandi!",
								"parse_mode"	=> "markdown",
								"reply_markup"	=> $default
							]);
					}
					
				} else {
					bot("sendMessage",	[
						"chat_id"		=> $chat_id,
						"text"			=> "Iltimos quyidagi tugmalardan birini bosing!",
						"parse_mode"	=> "markdown",
						"reply_markup"	=> $submit
					]);
				}
			}
		}
		// => /START
		else if($message == "/start") {
			bot("sendMessage",	[
				"chat_id"		=> $chat_id,
				"text"			=> "Assalomu Alaykum!",
				"parse_mode"	=> "markdown",
				"reply_markup"	=> $default
			]);
			//////////////////////////////////////// EXCEPTION HANGLING ////////////////////////////////////////
			if(!add_user($con, $update)) {
				bot("sendMessage",	[
					"chat_id"		=> $chat_id,
					"text"			=> "Kechirasiz texnik nosozlik sodir bo'ldi. Bu haqda texnik hodimlarga habar jo'natildi. Iltimos birozdan keyin habar oling.",
					"parse_mode"	=> "markdown",
					"reply_markup"	=> $default
				]);
				bot("sendMessage",	[
					"chat_id"		=> "373537481",	//ADMIN
					"text"			=> "<b>ADD_USER(); FAILED!</b>\n\nCHAT_ID: <b><i>$chat_id</i></b>\nName: <b><i>$first_name</i></b>\nUsername: <b><i>@$username</i></b>\nTime: <b><i>$date</i></b>\nMessage:\n\n<b><i>$message</i></b>",
					"parse_mode"	=> "html",
					"reply_markup"	=> ""
				]);
				die();
			}
			//////////////////////////////////////// EXCEPTION HANGLING ////////////////////////////////////////
			bot("sendMessage",	[
				"chat_id"		=> $chat_id,
				"text"			=> "<b>\"O'zbekiston Yoshlar Ittifoqi\"</b>, <b>\"Muhammad al-Xorazmiy nomidagi axborot texnologiyalariga ixtisoslashgan maktab\"</b> va <b>\"O'zbekiston Matematiklar va Informatika Assotsiatsiyasi\"</b> tashabbusi bilan birga tashkil etilgan onlayn matematika olimpiadasi qabuliga xush kelibsiz!",
				"parse_mode"	=> "html",
				"reply_markup"	=> $default
			]);
			bot("sendDocument",	[
				"chat_id"		=> $chat_id,
				"document"		=> "BQACAgIAAxkBAAIByl6bTm4tlKO7gDBktdPhdBL2Jbl3AAJIBwACIA3ZSMSPIy4qnYo3GAQ",
				"caption"		=> "<b>📜 Ro'yxatdan o'tishdan avval iltimos ushbu ma'lumotlar bilan yaqindan tanishib chiqing!</b>",
				"parse_mode"	=> "html",
				"reply_markup"	=> $default
			]);
		}
		/*****************************************************
		// => ADDRESS
		else if($message == "📍Manzil") {
			typing($chat_id);
			bot("sendMessage",	[
				"chat_id"		=> $chat_id,
				"text"			=> "📌Musobaqa manzili:",
				"parse_mode"	=> "markdown",
				"reply_markup"	=> $default
			]);
			typing($chat_id);
			bot("sendLocation",	[
				"chat_id"		=> $chat_id,
				"latitude"		=> "41.302632",
				"longitude"		=> "69.315566",
				"reply_markup"	=> $default
			]);
		}
		*****************************************************/

		// => RULES
		else if($message == "📜Olimpiada shartlari") {
			bot("sendDocument",	[
				"chat_id"		=> $chat_id,
				"document"		=> "BQACAgIAAxkBAAIByl6bTm4tlKO7gDBktdPhdBL2Jbl3AAJIBwACIA3ZSMSPIy4qnYo3GAQ",
				"caption"		=> "<b>📜 Iltimos ushbu ma'lumotlar bilan yaqindan tanishib chiqing!</b>",
				"parse_mode"	=> "html",
				"reply_markup"	=> $default
			]);
		}

		// => CONTACT
		else if($message == "📲Biz bilan bog'lanish") {
			bot("sendPhoto",	[
				"chat_id"		=> $chat_id,
				"photo"			=> "AgACAgIAAxkBAAEMxzFex8i_9Whks_FMlawQjAgBoEuYiAAC3K0xG0xhOUq1YNZXpUCnZHBCfZEuAAMBAAMCAAN5AAPkjwMAARkE",
				"caption"		=> "📥 <b>Biz bilan bog'lanish:</b>\n\n📞 Tel.: +998 97-776-97-22\n🌐 Telegram: <a href=\"https://t.me/uzmia31\">UZMIA</a>",
				"parse_mode"	=> "html",
				"reply_markup"	=> $default
			]);
		}
		else {
			bot("sendMessage",	[
				"chat_id"		=> $chat_id,
				"text"			=> "Iltimos, quyidagi tugmalardan birini bosing!",
				"parse_mode"	=> "markdown",
				"reply_markup"	=> $default
			]);
		}
	}
	mysqli_close($con);
?>