<?php 
/*
<?php
	$host = '';
	$user = '';
	$pass = '';
	$db = '';
?>
 */
include_once("configdb.php");
//'user'=>'id int auto_increment primary key ,userName varchar(250),pass varchar(255) ,groups int',
$tables = ['list'=>'id int auto_increment primary key,userid int, idtype int, idproduct int,idmeten int,iddate int, val varchar(255), prise  varchar(255),isActual int',
		   'types'=>'id int auto_increment primary key,`name` varchar(255),userid int,count int',
		   'products'=>'id int auto_increment primary key,name varchar(255),userid int,count int',
		   'metens'=>'id int auto_increment primary key,name varchar(255),userid int,count int',
		   'dates'=>'id int auto_increment primary key,name datetime,userid int,count int'];

$prefix = 'mybuh_'; //префикс для таблиц
$isDropTable = true; //удалять таблицы перед созданием

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
	die('Ошибка подключения (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}
else{
	if (!$mysqli->set_charset("utf8")) {
		    printf("Ошибка при загрузке набора символов utf8: %s\n", $mysqli->error);
		    //exit();
		} /*else {
		    printf("Текущий набор символов: %s\n", $mysqli->character_set_name());
		}*/
		else{
			$result = $mysqli->query("SET NAMES 'utf8'");
			if(!$result){
				echo "utf8 not set\n";
			}
		}

}

 ?>