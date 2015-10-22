<?php
	header('Content-type: application/json');

	$userEmail = $_POST['email'];
	$userPassword = $_POST['password'];
	$userFirstName = $_POST['firstName'];
	$userLastName = $_POST['lastName'];


	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "SkillsOnDemand";

	$connection = new mysqli($servername, $username, $password, $dbname);

	if ($connection->connect_error){
			header('HTTP/1.1 500 Bad connection to Database');
	    die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
	}
	else {
			$sql = "SELECT email FROM Client WHERE email = '$userEmail'";
			$result = $connection->query($sql);

			if ($result->num_rows > 0){
					header('HTTP/1.1 409 Conflict, Username already in use please select another one');
					die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
			}
			else {
					$sql = "INSERT INTO Client (fName, lName, email, passwrd)
					VALUES ('$userFirstName', '$userLastName', '$userEmail', '$userPassword')";

					if (mysqli_query($connection, $sql)){
						/*
						session_start();
					    if (! isset($_SESSION['firstName'])) {
					    	$_SESSION['firstName'] = $userFirstName;
					    }
					    if (! isset($_SESSION['lastName'])) {
					    	$_SESSION['lastName'] = $userLastName ;
					    }
					    */
						echo json_encode("New record created successfully");
					}
					else {
							header('HTTP/1.1 500 Bad connection, something went wrong while saving your data, please try again later');
							echo "Error: " . $sql . "\n" . mysqli_error($connection);
					}
			}
	}

?>