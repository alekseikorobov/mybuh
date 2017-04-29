<?php
	include_once("php/srv.php");

	if(isset($_GET['logoff'])){
		session_unset();
	}
	function html_alert($text){
		if($text == '') return $text;
		return '<div class="alert alert-danger" id="msg">
                        	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							  <span class="sr-only">Error:</span>
							  <span id="msg-text">'.$text.'</span>
                        </div>';
	}
	$userid =  $_SESSION['userid'];	
	$message = '';
	if(isset($_POST['input'])){
		if(!isset($_POST['username']) || !isset($_POST['password'])){
			$message= 'Все поля обязательны для заполнения';
		}
		$userid = getUserId($_POST['username'],$_POST['password']);
		//echo html_alert($userid);
		if($userid == 0){
			$message='Ошибка входа';
		} else{
			$_SESSION['userid'] = $userid;
		}
	}
	function chekin(){
		global $message;
		return html_alert($message);
	}
	if(isset($_GET['isDemo'])){
		$userid = getUserId('demo','');
		$_SESSION['userid'] = $userid;
		//echo html_alert($userid);
	}
	if(isset($userid) && $userid !== 0){		
		header("Location: data.html");
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script src="js/jquery/jquery-1.12.4.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>

	<link rel="stylesheet" type="text/css" href="lib/bootstrap-3.3.7-dist/css/bootstrap.css">
</head>
<body>
	<div class="container">
		<div style="margin-top:50px;" class="mainbox col-md-6 col-md-offset3 col-sm-8 col-sm-offset-2 ">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">Авторизация</div>
				</div>
				<div class="panel-body">
					<form method="post" action="index.php" class="form-horizontal" role="form">
						
						<div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="почта или уникальный идентификатор">    
                        </div>

                        <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="password" type="password" class="form-control" name="password" value="" placeholder="пароль">    
                        </div>
                        <div style="margin-top:10px" class="form-group">
                            <!-- Button -->
                            <div class="col-sm-12 controls">
                            	<input type="submit" value="Войти" class="btn btn-success" name="input">

                            	<a id="btn-login" href="regist.php" style="padding: 15px 15px;">Регистрация</a>
                            	<a id="btn-login" href="index.php?isDemo=1" style="padding: 15px 15px;">Demo</a>
                            </div>
                        </div>
                        <?php
                        	echo chekin();
                        ?>                        
					</form>					
				</div>
			</div>
		</div>
	</div>	
</body>
</html>
