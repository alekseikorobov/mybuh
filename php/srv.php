<?php 
	session_start();
	/*$d = "2016-10-29 00:00:00";
	
	$d1 = date($d);
	echo date("Y/m/d",strtotime($d));
	return;*/
	
	include_once("worker.php");

	function is_session_started()
	{
		return PHP_SESSION_ACTIVE;
	    if ( php_sapi_name() !== 'cli' ) {
	        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
	            return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
	        } else {
	            return session_id() === '' ? FALSE : TRUE;
	        }
	    }
	    return FALSE;
	}

	if(isset($_SESSION['userid'])){
	//if(is_session_started() == 2){
		$userid = $_SESSION['userid'];
	}

	//$userid = 1;

	//$_POST['getDataAll'] = 1;
	//$userid = 1;

	if(isset($_POST['getDataAll'])){
		//echo "getDataAll\n";

		$list = getListData(date("Y/m/d"));		
		$types = getAllData("types");
		$products = getAllData("products");
		$metens = getAllData("metens");
		$dates = getAllData("dates",date("Y/m/d"));

		$userName = getUserName();

		$res = array('list' => $list, 
					'types' => $types,
					'products' => $products,
					'metens' => $metens,
					 'dates' => $dates,
					 'userName' =>$userName
					);
		echo json_encode($res, JSON_UNESCAPED_UNICODE)."\n";
	}	

	if(isset($_POST['dataFind'])){
		$value = $_POST['value'];
		if(!$value) return;
		if($value == '') return;

		$list = findData($value);

		$res = array('list' => $list);
		echo json_encode($res, JSON_UNESCAPED_UNICODE)."\n";

	}
	if(isset($_POST['getMetaDataAll'])){
		//echo "getMetaDataAll\n";

		$types = getAllData("types");
		$products = getAllData("products");
		$metens = getAllData("metens");
		$dates = getAllData("dates");

		$res = array('types' => $types,
					'products' => $products,
					'metens' => $metens,
					'dates' => $dates
					);
		echo json_encode($res, JSON_UNESCAPED_UNICODE)."\n";
	}

	/*-----тестовые данные */
	//alltruncate();

	// $_POST['insert'] = 1;

	// $_POST['idmeten'] = -1;	
	// $_POST['idtype'] = -1;	
	// $_POST['idproduct'] = -1;

	// $_POST['date(format)'] = "2016/10/29";
	// $_POST['iddate'] = 1;
	// $_POST['val'] = "100";
	// $_POST['prise'] = "89";

	// $_POST['meten'] = 'г';
	// $_POST['type'] = 'Продукты';
	// $_POST['product'] = 'Шоколад';

	/*------------------------*/

	if(isset($_POST['insert'])){
		
		$idmeten = insertData("metens",$_POST['idmeten'],$_POST['meten']);
		$idtype = insertData("types",$_POST['idtype'],$_POST['type']);
		$idproduct = insertData("products",$_POST['idproduct'],$_POST['product']);

		$iddate = insertData("dates",$_POST['iddate'],$_POST['date']);

		$idList = insertList($iddate,$idmeten,$idtype,$idproduct,$_POST['val'],$_POST['prise'],$_POST['insert']);
		$res = array('DalaListId' => $idList,
				   'idtype' => $idtype,
				   'idproduct' => $idproduct,
				   'idmeten' => $idmeten,
				   'iddate' => $iddate);

		echo json_encode($res)."\n";
	}

	//var data = {"getData": true,"strDate",strDate};
	//$_POST['getData'] = 1;
	//$_POST['strDate'] = "2016/10/28";

	if(isset($_POST['getData'])){
		$list = getListData($_POST['strDate']);
		$iddate = getData("dates",$_POST['strDate']);
		$res = array('list' => $list,
				   'iddate' => $iddate);
		echo json_encode($res)."\n";
	}


	//$_POST['ModDataRow'] = "setArchiveRow":"recoveRow";
	//$_POST['id'] = 1;
	if(isset($_POST['ModDataRow'])){
		if($_POST['ModDataRow'] == "setArchiveRow"){
			echo setArchiveRow($_POST['id'])."\n";	
		}
		else if($_POST['ModDataRow'] == "recoveRow"){
			echo recoveRow($_POST['id'])."\n";
		}		
	}

	//$_POST['getStatistic'] = 1;
	//$_POST['date'] = "2017.02.01";
	//$userid=1;
	if(isset($_POST['getStatistic'])){
		$date = $_POST['date'];

		$list = getStat($date);
		$res = array('list' => $list);

		echo json_encode($res, JSON_UNESCAPED_UNICODE)."\n";
	}
?>