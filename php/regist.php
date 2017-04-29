<?php
	require_once('php/recaptchalib.php');
	if(isset($_POST["g-recaptcha-response"])){	
		  $privatekey = "6Le9LhAUAAAAAG83SJjk0Xzk-l2tUVevY8IL1icV";
		  $resp = recaptcha_check_answer ($privatekey,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["g-recaptcha-response"]);

		  if (!$resp->is_valid) {
		    // What happens when the CAPTCHA was entered incorrectly
		    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
		         "(reCAPTCHA said: " . $resp->error . ")");
		    echo "Регистрация не удалась, попробуйте снова";
		  } else {
		  		echo "Регистрация возможна";
		    // Your code here to handle a successful verification
		  }	
	  }
?>



<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
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
	</script>>

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

                        <div class="g-recaptcha" data-sitekey="6Le9LhAUAAAAAFqPQn0fK1a5fTdsPARpbsDThoPN"></div>
						<!-- <div class="g-recaptcha" data-sitekey="6Le9LhAUAAAAAFqPQn0fK1a5fTdsPARpbsDThoPN"></div> -->

                        <div style="margin-top:10px" class="form-group">
                            <!-- Button -->
                            <div class="col-sm-12 controls">
                            	<input type="submit" value="Регистрация" class="btn btn-success">
                              <!-- <a id="btn-login" href="#" onclick="sendRegist(); return false;" >Регистрация</a> -->
                            </div>
                        </div>
                        <div class="alert alert-danger" style="display: none;" id='msg'>
                        	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
							  <span class="sr-only">Error:</span>
							  <span id="msg-text"></span>
                        </div>
					</form>
					
				</div>
			</div>
		</div>
	</div>
	
	
</body>
</html>