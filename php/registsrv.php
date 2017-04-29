<?php
	/*if (!function_exists('getallheaders')) 
	{ 
	    function getallheaders() 
	    { 
	           $headers = ''; 
	       foreach ($_SERVER as $name => $value) 
	       { 
	           if (substr($name, 0, 5) == 'HTTP_') 
	           { 
	               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
	           } 
	       } 
	       return $headers; 
	    } 
	} 
	$headers = getallheaders();
	foreach ($headers as $name => $value) {
	    echo "$name: $value\n<br>";
	}*/

	$recaptcha = $_POST['g-recaptcha-response'];
	$postdata = http_build_query(
	  array(
	      'secret' => '6Le9LhAUAAAAAG83SJjk0Xzk-l2tUVevY8IL1icV',
	      'response' => $recaptcha
	  )
	);

	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => "Content-Type: text/json\r\n",
			//"Authorization: Basic ".base64_encode("$https_user:$https_password")."\r\n",
			'content' => $postdata
			,'timeout' => 120
			)
		);
	                    
	$context  = stream_context_create($opts);
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$result = file_get_contents($url, false, $context, -1, 40000);

	echo "$result";
	$obj = json_decode($result);
	if($obj->success)
	{
		$mail = '';
		$pass = '';

		//registration($mail,$pass);
		echo "Регистрация возможна";
		//http_redirect();
	}

	echo "Регистрация не удалась, попробуйте снова";
?>