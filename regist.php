<?php
	session_start();
	require_once('php/recaptchalib.php');
	require_once('php/worker.php');
	
	$publickey = "6Le9LhAUAAAAAFqPQn0fK1a5fTdsPARpbsDThoPN";
	$privatekey = "6Le9LhAUAAAAAG83SJjk0Xzk-l2tUVevY8IL1icV";

	function html_alert($text){
		if($text == '') return $text;
		return '<div class="alert alert-danger" id="msg">
                        	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							  <span class="sr-only">Error:</span>
							  <span id="msg-text">'.$text.'</span>
                        </div>';
	}
	//$_POST['username'] = '111';
	//$_POST['password'] = '222';
	function registINsert(){
  		$login = $_POST['username'];
		$pass = $_POST['password'];

		if(!isset($login) || !isset($pass)){
			return 'Регистрация не удалась, все поля должны быть заполнены';
		}

  		if(anyRow('Users','mail',$login)){
			return 'Регистрация не удалась, пользователь с таким именем уже существует';
		}
		$userid = InsertUser($login, $pass);
		if($userid > 0){
	  		$_SESSION['userid'] = $userid;		  		
	  		header("Location: data.html");
  		}
  		return 'Ошибка регистрации ';
	  	
	}
	$message = '';
	//$_POST["recaptcha_response_field"] = 1;

	if(isset($_POST["recaptcha_response_field"])){
		$message = registINsert();
		try {			  
			  $resp = recaptcha_check_answer ($privatekey,
			                                $_SERVER["REMOTE_ADDR"],
			                                $_POST["recaptcha_challenge_field"],
			                                $_POST["recaptcha_response_field"]);

			  if (!$resp->is_valid) {
			    $message = "Регистрация не удалась, не верный ввод с изображения";
			  } else {
			  	$message = registINsert();
			 }
		}
		catch (Exception $e) {
			$message = "$e";
		}
	}
	function regsend(){
		global $message;
		return html_alert($message);
	}

	//echo html_alert('message');
?>


L A M P

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
	<script src="js/jquery-1.12.4.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>

	<link rel="stylesheet" type="text/css" href="lib/bootstrap-3.3.7-dist/css/bootstrap.css">

	<script type="text/javascript">
		function sendRegist(){		
			var login = $('#login-username').val();
			var pass = $('#password').val();
			var recaptcha = $('#g-recaptcha-response').val();
			var data = {"login": login,"pass":pass,'g-recaptcha-response':recaptcha};
			//console.log(data);
			$.ajax({
				url: "php/registsrv.php",
				type: 'POST',
				//dataType: 'application/json',
				//async:true,
				beforeSend: function( jqXHR, settings ){
					//проверка перед отправкой, если return false, то не отправлять!
					return true;
				},
				data:data,
				success: function (data, textStatus, jqXHR) {
					if(data && data !== ''){
						$('#msg').show(200);
						$('#msg-text').html(data);
					}
				},
				error:function(jqXHR,textStatus,errorThrown ){
					console.error(jqXHR);
				}
			});
		}
	</script>

</head>
<body>
	<div class="container">
		<div style="margin-top:50px;" class="mainbox col-md-6 col-md-offset3 col-sm-8 col-sm-offset-2 ">
			<div class="panel panel-info">
				<div class="panel-heading">
					<div class="panel-title">Регистрация</div>
				</div>
				<div class="panel-body">
					<form method="post" action="regist.php" class="form-horizontal" role="form">
						
						<div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="почта или уникальный идентификатор">    
                        </div>

                        <div style="margin-bottom: 25px" class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input id="password" type="password" class="form-control" name="password" value="" placeholder="пароль">    
                        </div>

                        <?php				          
				          echo recaptcha_get_html($publickey);
				        ?>

                        <div style="margin-top:10px" class="form-group">
                            <!-- Button -->

                            <div class="col-sm-12 controls">
                            	<input type="submit" value="Регистрация" class="btn btn-success">
                            </div>
                        </div>
                        <?php
                        	echo regsend();
                        ?>                        
					</form>					
				</div>
			</div>
		</div>
	</div>	
</body>
</html>