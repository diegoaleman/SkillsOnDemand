<?php
	header('Content-type: application/json');
	$email = $_POST['email'];
	$userPassword = $_POST['password'];

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "SkillsOnDemand";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	
	// Check connection
	if ($conn->connect_error) 
	{
	    header('HTTP/1.1 500 Bad connection to Database');
	    die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
	}
	else
	{
		$sql = "SELECT fName, lName FROM Client WHERE email = '$email' AND passwrd = '$userPassword'";
		$result = $conn->query($sql);

		
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc()) 
		    {
		    	$response = array('fName' => $row['fName'], 'lName' => $row['lName']);   

				// // Setting the cookies
				// if ($rememberMe == "true")
				// {
			 //   		setcookie("cookieUsername", "$email", time() + 60*60*24*20, "/");
			 //   		setcookie("cookiePassword", "$userPassword", time() + 60*60*24*20, "/");
			 //    }

			 //    // Starting the session
			 //    session_start();
			 //    if (! isset($_SESSION['firstName']))
			 //    {
			 //    	$_SESSION['firstName'] = $row['fName'];
			 //    }
			 //    if (! isset($_SESSION['lastName']))
			 //    {
			 //    	$_SESSION['lastName'] = $row['lName'];
			 //    }
			 //    if (! isset($_SESSION['email']))
			 //    {
			 //    	$_SESSION['email'] = $email;
			 //    }
			    
			    echo json_encode($response);
			}
		    //echo json_encode($result->fetch_assoc());
		}
		else
		{
	    	header('HTTP/1.1 406 Username or password are incorrect. Please try again.');
	        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
		}
	} 

?>