<?php

	include_once("connect.php");
	include_once("worker.php");	

	/**
	 * registration in to DB
	 * @param $login - login unicue name
	 * @param -$pass password
	 * @return - 1 - if exist row, 0 - ok, 2 - error
	 */
	function registration($login, $pass){

		try {
			if(anyRow('Users','mail',$login)){
				return 1;
			}
		
			InsertUser($login, $pass);
			return 0;
			
		} catch (Exception $e) {
			return 2;			
		}
	}

?>