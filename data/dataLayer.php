<?php

	function connect()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "SkillsOnDemand";

		$connection = new mysqli($servername, $username, $password, $dbname);
	
		// Check connection
		if ($connection->connect_error) 
		{
		    return null;
		}
		else
		{
			return $connection;
		}
	}

	function errors($type)
	{
		$header = "HTTP/1.1 ";

		switch($type)
		{
			case 500:	$header .= "500 Bad connection to Database";
						break;
			case 206:	$header .= "206 Wrong Credentials";
						break;
			case 406:	$header .= "406 User Not Found";
						break;
			case 409:	$header .= "409 Conflict, Username already in use please select another one";
						break;
			case 417:	$header .= "417 No content set in the cookie/session";
						break;
			default:	$header .= "404 Request Not Found";
		}

		header($header);
		return array('message' => 'ERROR', 'code' => $type);
	}

	function login($email)
    {
        $conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT email, fName, lName, passwrd FROM Client WHERE email = '$email'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc()) 
		    	{
		    		$response = array('message' => 'OK', 'fName' => $row['fName'], 'lName' => $row['lName'], 'email' => $row['email'], 'password' => $row['passwrd']);   
		    	}

		    	$conn->close();
		    	return $response;
			}
			else
			{
				$conn->close();
				return errors(406);
			}
        }
        else
        {
        	$conn->close();
        	return errors(500);
        }
    }

    function register($email, $password, $firstName, $lastName)
    {
    	$conn = connect();

    	if ($conn != null) 
    	{
    		
    		$sql = "SELECT email FROM Client WHERE email = '$email'";
			$result = $conn->query($sql);


    		if ($result->num_rows > 0)
			{
				$conn->close();
				return errors(409);
			}
			else 
			{
	    		$sql = "INSERT INTO Client (fName, lName, email, passwrd) VALUES ('$firstName', '$lastName', '$email', '$password')";
	    	
		    	if (mysqli_query($conn, $sql)) 
	    		{
				    $response  = array('message' => 'OK');
				}

				$conn->close();
				return $response;
			}

    	}
    	else
    	{
    		$conn->close();
    		return errors(500);
    	}
    }

    function newPost($title, $description, $firstname, $lastname, $email)
    {
    	$conn = connect();

    	if ($conn != null)
    	{
    		$sql = "SELECT email FROM Post WHERE email = '$email'";
    		$result = $conn->query($sql);

    		if ($result->num_rows > 0)
    		{
    			$conn->close();
    			return errors(409);
    		}
    		else
    		{
    			$sql = "INSERT INTO Post (title, description, fName, lName, email) VALUES ('$title', '$description', '$firstName', '$lastName', '$email'";

    			if (mysqli_query($conn, $sql)) 
    			{
    				$response = array('message' => 'OK');
    			}

    			$conn->close();
    			return $response;
    		}
    	}
    	else
    	{
    		$conn->close();
    		return errors(500);
    	}
    }

    function startSession($fName, $lName, $email)
    {
		// Starting the session
	    session_start();
	    if (! isset($_SESSION['firstName']))
	    {
	    	$_SESSION['firstName'] = $fName;
	    }
	    if (! isset($_SESSION['lastName']))
	    {
	    	$_SESSION['lastName'] = $lName;
	    }
	    if (! isset($_SESSION['email']))
	    {
	    	$_SESSION['email'] = $email;
	    }
    }

    function getSession()
    {
    	session_start();
    	if (isset($_SESSION['firstName'])) {
    		$response = array('message' => 'OK' ,'name' => $_SESSION['firstName']);
    		return $response;
    	} else {
    		$response = array('name' => 'user');
    		return $response;
    	}
    }

    function getCartItems($email, $status)
    {
    	$conn = connect();

        if ($conn != null)
        {

        	if ($status == 'P') {
        		// Get cart elements 
        		$sql = "SELECT * FROM Cart WHERE email = '$email' AND status = 'P'";
        	} elseif ($status == 'B') {
        		// Get cart elements 
        		$sql = "SELECT * FROM Cart WHERE email = '$email' AND status = 'B'";
        	}

        	$result = $conn->query($sql);

        	if ($result->num_rows > 0)
			{

				$response;
				$data = array();
				while($row = $result->fetch_assoc()) 
		    	{
		    		// Get email and skill to display on table
		    		$rowContent = $row['skill'];

		    		$sql2 = "SELECT * FROM Skill WHERE skillId = '$rowContent'";

		    		$result2 = $conn->query($sql2);

		    		if ($result2->num_rows > 0) {
		    			$secondData = array(); 

		    			while ($row2 = $result2->fetch_assoc()) {

		    				$rowEmail = $row2['email'];

		    				// Get client info based on email o display name on cart
		    				$sql3 = "SELECT * FROM Client WHERE email = '$rowEmail'";

		    				$result3 = $conn->query($sql3);

		    				if ($result3->num_rows > 0) {
		    					$thirdData = array();

		    					while ($row3 = $result3->fetch_assoc()) {
		    						array_push($thirdData, array('name' => $row3['fName'], 'last' => $row3['lName']));
		    					}
		    				}

		    				array_push($secondData, array('sEmail' => $row2['email'], 'sTitle' => $row2['title']));
		    			}
		    		}

		    		array_push($data, array('orderId' => $row['orderId'], 'quantity' => $row['quantity']));
		    	}

		    	$response = array('message' => 'OK', 'data' => $data, 'secondData' => $secondData, 'thirdData' => $thirdData);
		    	$conn->close();
		    	return $response;
			}
			else
			{
				return array('message' => 'NONE');   
			}

        }
        else
        {
        	$conn->close();
        	return errors(500);
        }
    }


    function addSkill ($data, $email){
    	$conn = connect();

    	if ($conn != null){

    		
    			$title = $data['title'];
    			$quantity = $data['quantity'];
    			$description = $data['description'];
    			$category = $data['category'];
    			$sql = "INSERT INTO Skill(title, description, email, category, quantity) VALUES ('$title','$description','$email', '$category', '$quantity')";
    			if (mysqli_query($conn, $sql)) {
		    		$conn->close();
				    return array("status" => "COMPLETE");
				} 
				else {
					$conn->close();
					return errors(409);
				}
			
			
        }
        else {
        	$conn->close();
        	return errors(500);
        }
    }


    function getAllSkills(){
    	$conn = connect();

        if ($conn != null) {
        	$sql = "SELECT * FROM Skill";
			$result = $conn->query($sql);
			
			# Items exist
			$response = array();
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					array_push($response,array('skillId' => $row['skillId'], 'title' => $row['title'], 'description' => $row['description'],'email' => $row['email'],'category' => $row['category'], 'quantity' => $row['quantity']));
				}
				return $response;
			}
			else {
				$conn->close();
				return $response; // no existen items
			}
        }
        else {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

?>