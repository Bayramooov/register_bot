<?php
	if(isset($_GET["file"])) {
		$file = $_GET["file"];

		if(file_exists("$file/name.log") && file_exists("$file/level.log") && file_exists("$file/phone.log")) {
			$name = file_get_contents("$file/name.log");
			$level = file_get_contents("$file/level.log");
			$phone = file_get_contents("$file/phone.log");
			echo "Successfully loaded the files:" ."<br>";
		} else {
			delete_files($file);
			die("Not enough info, File Deleted!");
		}

		$regs = file_get_contents("regs.log");
		echo $file ."<br>";
		echo $name ."<br>";
		echo $level ."<br>";
		echo $phone ."<br>";
		echo $regs ."<br>";

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
		delete_files($file);
	} else {
		echo "Please, mention the file! ...?file=< File Name >";
	}

	echo "<div style=\"display: none;\">";

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

	echo "</div>";
?>