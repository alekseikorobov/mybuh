<?php
	include_once("connect.php");	

	function correctString($str){
		$str = str_replace("'", "", $str);
		return $str;
	}

	function insertData($table,$id,$val){
		global $prefix,$mysqli,$userid;
		$isInsert = false;
		$isUpdate = false;
		$count = 1;
		
		$table = correctString($table);
		$id = correctString($id);
		$val = correctString($val);
		//echo "\n$id\n";
		if($id == -1){
			$sql = "select id,count from $prefix"."$table where userid=$userid and name='$val'";
			
			$result = $mysqli->query($sql);
			if($result){
				if($result->num_rows == 0){
					$isInsert = true;
				}else{
					$r = $result->fetch_row();
					$id = $r[0];
					$count = $r[1]+1;
					$isUpdate = true;
				}
			}
		}
		else{
			$sql = "select name,count from $prefix"."$table where userid=$userid and id=$id";
			//echo "$sql\n";
			$result = $mysqli->query($sql);
			if($result){
				if($result->num_rows == 0){
					$isInsert = true;
				}else{
					$r = $result->fetch_row();
					$dt = $r[0];
					if($table == "dates"){
						$dt = date("Y/m/d",strtotime($dt));
					}
					//echo "$dt $val \n";
					if($dt != $val){
						$isInsert = true;
					}
					else{
						$count = $r[1] + 1;
						$isUpdate = true;		
					}
				}
			}
		}
		if($isUpdate){
			$sql = "update $prefix$table set count = $count where id = $id";
			//echo "$sql\n";
			$result = $mysqli->query($sql);
			if ($mysqli->errno) {
				die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
			}
		}
		if($isInsert){
			$sql = "insert into $prefix$table(name,userid,count) value('$val',$userid,1)";
			//echo "$sql\n";
			$result = $mysqli->query($sql);
			if ($mysqli->errno) {
				die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
			}
			if(!$mysqli->insert_id){
				$id = -1;
			}
			else{
				$id = $mysqli->insert_id;
			}
		}
		//echo "sql $sql\n";
		//echo "id $id\n";
		return $id;
	}

	function insertList($iddate,$idmeten,$idtype,$idproduct,$val,$prise,$id){
		global $prefix,$mysqli,$userid;
		$idList = 0;

		if($id == -1){
			$sql = "insert into ".$prefix."list(userid,idtype,idproduct,idmeten,iddate,val,prise,isActual) value($userid,$idtype,$idproduct,$idmeten,$iddate,'$val','$prise',1)";
		}
		else{
			$sql = "update ".$prefix."list set isActual = 1,
			iddate = $iddate,idmeten = $idmeten,idtype = $idtype,idproduct = $idproduct,
			val = '$val',prise = '$prise' where id = $id";
		}

		//echo "insertList $sql \n";

		$result = $mysqli->query($sql);

		if ($mysqli->errno) {
			die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
		}
		if($id == -1){
			if(!$mysqli->insert_id){
				$idList = -1;
			}
			else{
				$idList = $mysqli->insert_id;
			}
		}
		else{
			$idList = $id;
		}

		return $idList;
	}

	function getData($table,$val){
		global $prefix,$mysqli,$userid;
		$isInsert = false;
		$sql = "select id from $prefix$table where userid=$userid and name='$val'";

		$result = $mysqli->query($sql);
		if($result){
			if($result->num_rows == 0){
				$isInsert = true;
			}else{
				$r = $result->fetch_row();
				$id = $r[0];
			}
		}	
		return $id;
	}

	function alltruncate(){
		global $prefix,$mysqli,$tables;
		foreach ($tables as $table => $pole) {
			
			$sql = "truncate table $prefix$table";				
			$result = $mysqli->query($sql);
			if($result){
				echo "Успешно очищена таблица $prefix$table\n";
			}
		}
	}
	function getAllData($table,$val = null){
		global $prefix,$mysqli,$userid;
		$w = "";
		if(!is_null($val)){
			$w = " and name = '$val'";
		}
		$sql = "";
		if($table == "products"){
			///запрос для получения дополнительных сведений о продукте
			$sql = "select l1.idproduct as id,l1.name,l2.idmeten as lastidmeten,l2.val as lastval,l2.prise as lastprise
				from (
					select l.idproduct,  p.name, max(l.id) as id from ".$prefix."list l join $prefix$table p on l.idproduct=p.id
				    where l.userid = $userid and l.isActual = 1 group by p.name,l.idproduct
					) l1 join ".$prefix."list l2 on l1.id = l2.id;";
		}
		else if($table == "dates"){
			$sql = "select id, DATE_FORMAT(name,'%Y/%m/%e') as name from $prefix$table where userid = $userid".$w;
		}
		else{
			$sql = "select id, name from $prefix$table where userid = $userid".$w;
		}		
		//echo "\n\n$sql\n\n";
		$result = $mysqli->query($sql,MYSQLI_STORE_RESULT);
		if ($mysqli->errno) {
			die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
		}
		else{
			$res = array();
			if($result){
				return getArray($result);
			}
			else{
				return "not result";	
			}
		}
	}
	function findData($value){
		global $mysqli,$prefix,$userid;

		$value = correctString($value);

		$sql = "select l.id,l.iddate,l.idmeten,l.idproduct,l.idtype,l.isActual,l.prise,l.val from ".$prefix."list l join ".$prefix."products p on l.idproduct = p.id where l.userid = $userid and p.name like '%$value%' and l.isActual = 1
							order by l.iddate"
							;
		if(!$mysqli){
			return "mysqli";
		}		
		$result = $mysqli->query($sql,MYSQLI_STORE_RESULT);
		
		if($result){
			return getArray($result);			
		}
		else{
			return "not result";	
		}
	}

	function getListData($date){
		global $mysqli,$prefix,$userid;

		$sql = "select l.id,l.iddate,l.idmeten,l.idproduct,l.idtype,l.isActual,l.prise,l.val from ".$prefix."list l join ".$prefix."dates d on l.iddate = d.id where l.userid = $userid and d.name = '$date' and l.isActual = 1";
		if(!$mysqli){
			return "mysqli";
		}		
		$result = $mysqli->query($sql,MYSQLI_STORE_RESULT);
		
		if($result){
			return getArray($result);			
		}
		else{
			return "not result";	
		}
	}
	function getArray($result){
		$res = array();
		if(!method_exists($result,'fetch_all')){				
			while ($row = $result->fetch_assoc()) {
			    $res[] = $row;
			}
		}
		else{
			$res = $result->fetch_all(MYSQLI_ASSOC);
		}
		return $res;
	}
	function utf8_array_decode($input){
		global $prefix,$mysqli,$userid;
		$return = array();
		foreach ($input as $key => $val) {
			$k = utf8_decode($key);
			//echo "\n\n$val\n\n";
			$return[$k] = utf8_decode($val);
		}
		return $return;           
    }

    function setArchiveRow($id){
    	global $prefix,$mysqli;
    	///пометить запись как аривную
    	$sql = "update ".$prefix."list set isActual = -1 where id = $id";
    	$result = $mysqli->query($sql);
    	if(!$result){
    		if ($mysqli->errno) {
				die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
			}
    	}
    	else{
    		return "ok";
    	}
    }
    function recoveRow($id){
    	global $prefix,$mysqli;
    	///вернуть запись из архфива
    	$sql = "update ".$prefix."list set isActual = 1 where id = $id";
    	$result = $mysqli->query($sql);
    	if(!$result){
    		if ($mysqli->errno) {
				die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
			}
    	}
    	else{
    		return "ok";
    	}
    }

    function anyRow($table,$pole,$val){
    	global $mysqli;
    	$sql = "select 1 from $table where $pole = '$val'";
		//echo "\n\n$sql\n\n";
		$result = $mysqli->query($sql);
		//if(method_exists($result,'fetch_assoc')){
			while ($row = $result->fetch_assoc()) {
			    return true;
			}
		//}
		return false;
    }

    function InsertUser($login,$password){
    	global $mysqli;
    	$d = date("Y-m-d",time());
        $password  = sha1($password);
        $sql = "INSERT INTO Users(mail,`time`,pass) VALUES ('$login','$d', '$password');";
        $id = 0;
        $result = $mysqli->query($sql);
		if ($mysqli->errno) {
			die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
		}
		if(!$mysqli->insert_id){
			$id = -1;
		}
		else{
			$id = $mysqli->insert_id;
		}
        return $id;
    }
    function getUserId($login,$password){
			global $mysqli;
    	$password  = sha1($password);    	
    	if($login == 'demo'){
    			$sql = "select user_id from Users where mail = '$login'";
    	}
    	else{
    		$sql = "select user_id from Users where mail = '$login' and pass='$password'";
    	}
			$result = $mysqli->query($sql);
			if($result){
				if($result->num_rows == 0)
					return 0;
				
				$r = $result->fetch_row();
				return $r[0];
			}
			else{
				return 0;
			}
    }
    function getUserName(){
    	global $mysqli,$userid;
    	$sql = "select mail from Users where user_id = $userid";
			$result = $mysqli->query($sql);
			if($result){
				if($result->num_rows == 0)
					return 0;
				
				$r = $result->fetch_row();
				return $r[0];
			}
			else{
				return '';
			}
    }
    function getStat($date){
    	global $mysqli,$userid,$prefix;
    	if(!$date){
    		$date = "NOW()";
    	}else{
    		$date = "'$date'";
    	}

    	$sql = "select typ.name,count(*) as c,sum(t.prise) as prise
							from $prefix"."list t join $prefix"."dates d on t.iddate=d.id
																join $prefix"."types typ on t.idtype=typ.id
							WHERE YEAR( d.name ) = YEAR( $date ) 
							AND MONTH( d.name ) = MONTH( $date ) 
							and t.userid = $userid GROUP BY typ.name";

			$result = $mysqli->query($sql,MYSQLI_STORE_RESULT);

			if ($mysqli->errno) {
				die('Select Error (' . $mysqli->errno . ') ' . $mysqli->error);
			}
		
			if($result){
				return getArray($result);			
			}
			else{
				return "not result";	
			}
    }
?>