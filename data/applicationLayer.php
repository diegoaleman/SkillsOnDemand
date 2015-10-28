<?php

	header('Content-type: application/json');
	require_once __DIR__ . '/dataLayer.php';
	
	$action = $_POST['action'];

	switch($action)
	{
		case 'LOGIN':	loginAction();
						break;
		case 'REGISTER': registerAction();
						break;
	}

	function loginAction()
	{
		$email = $_POST['email'];
		$pass = $_POST['password'];

		$result = login($email);

		if ($result['message'] == 'OK')
		{
		    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	    
		    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    	
	    	# --- DECRYPTION ---
		    $ciphertext_dec = base64_decode($result['password']);
		    
		    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
		    
		    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

		    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
		   	
		   	$count = 0;
		   	$length = strlen($plaintext_dec);

		    for ($i = $length - 1; $i >= 0; $i --)
		    {
		    	if (ord($plaintext_dec{$i}) === 0)
		    	{
		    		$count ++;
		    	}
		    }

		    $plaintext_dec = substr($plaintext_dec, 0,  $length - $count);

		   if ($plaintext_dec === $pass)
		   {	
		    	$response = array('fName' => $result['fName'], 'lName' => $result['lName']);   
			    
			 //    # Starting the sesion (At the server)
		  //   	startSession($result['fName'], $result['lName'], $result['email']);

			 //    # Setting the cookies
				// setcookie("cookieUsername", $result['email']);
			    
			    echo json_encode($response);
			}
			else
			{
				die(json_encode(errors(206)));
			}
		}
		else
		{
			die(json_encode($result));
		}

	}

	function registerAction()
	{
		$email = $_POST['email'];
		$pass = $_POST['password'];
		$first = $_POST['firstName'];
		$last = $_POST['lastName'];

		# --- ENCRYPTION ---
	    # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
	    # convert a string into a key
	    # key is specified using hexadecimal
	    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	    
	    # show key size use either 16, 24 or 32 byte keys for AES-128, 192
	    # and 256 respectively
	    $key_size =  strlen($key);
	    
	    $plaintext = $pass;

	    # create a random IV to use with CBC encoding
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    
	    # creates a cipher text compatible with AES (Rijndael block size = 128)
	    # to keep the text confidential 
	    # only suitable for encoded input that never ends with value 00h
	    # (because of default zero padding)
	    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,
	                                 $plaintext, MCRYPT_MODE_CBC, $iv);

	    # prepend the IV for it to be available for decryption
	    $ciphertext = $iv . $ciphertext;
	    
	    # encode the resulting cipher text so it can be represented by a string
	    $password_ciphertext_base64 = base64_encode($ciphertext);


		$result = register($email, $password_ciphertext_base64, $first, $last);

		if ($result['message'] == 'OK') 
		{
			echo json_encode("Registration successfull");
		}
		else
		{
			die(json_encode($result));
		}
	}

?>