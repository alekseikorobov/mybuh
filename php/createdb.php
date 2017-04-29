<?php 
	include_once("connect.php");

	foreach ($tables as $table => $pole) {
		if($isDropTable){
			$sql = "drop table $prefix$table";				
			$result = $mysqli->query($sql); // or die('error')
			if($result){
				echo "Успешно удалена таблица $prefix$table\n";
			}
		}

		$sql = "create table if not exists $prefix$table($pole) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		$result = $mysqli->query($sql); // or die('error')
		if($result){
			echo "Успешно создана таблица $prefix$table\n";
		}
		else{
			echo "Не создана таблица $prefix$table ".$mysqli->error."\n";
		}
	}	
	
	echo "\n";
?>