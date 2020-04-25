<?php
	require_once("../private.php");
	require_once("../funcs.php");
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
	$folders = scandir("../temp", SCANDIR_SORT_NONE);
	foreach ($folders as $id) {
		if($id != "." && $id != ".."){
			$result = array();
			$message = "<b>TIMEOUT!</b> Ro'yxatdan o'tish bekor qilindi! Qayta ro'yxatdan o'tish uchun <b>'👥Ro'yxatdan o'tish'</b> tugmasini bosing.";
			$request = URL .API_KEY ."/sendMessage?chat_id=" .$id ."&text=" .$message ."&parse_mode=html" ."&reply_markup=" .$default;
			$result = json_decode(file_get_contents($request), true);
			sleep(0.5);
			if($result['ok'] == true) {
?>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 455.431 455.431" style="width: 18px; enable-background:new 0 0 455.431 455.431;" xml:space="preserve"><path style="fill:#8DC640;" d="M405.493,412.764c-69.689,56.889-287.289,56.889-355.556,0c-69.689-56.889-62.578-300.089,0-364.089s292.978-64,355.556,0S475.182,355.876,405.493,412.764z"/><g style="opacity:0.2;"><path style="fill:#FFFFFF;" d="M229.138,313.209c-62.578,49.778-132.267,75.378-197.689,76.8c-48.356-82.489-38.4-283.022,18.489-341.333c51.2-52.622,211.911-62.578,304.356-29.867C377.049,112.676,330.116,232.142,229.138,313.209z"/></g><path style="fill:#FFFFFF;" d="M195.004,354.453c-9.956,0-19.911-4.267-25.6-12.8l-79.644-102.4c-11.378-14.222-8.533-34.133,5.689-45.511s34.133-8.533,45.511,5.689l54.044,69.689l119.467-155.022c11.378-14.222,31.289-17.067,45.511-5.689s17.067,31.289,5.689,45.511L220.604,341.653C213.493,348.764,204.96,354.453,195.004,354.453z"/><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
<?php
				echo $id ."<br>";
			}
			echo "<div style=\"display: none\">";
			delete_files("../temp/$id");
			echo "</div>";
		}
	}
?>