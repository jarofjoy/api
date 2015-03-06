<?php 
	require_once 'config.php';
	$pretty=false;
	if(isset($pretty))
	{
		$pretty=$_GET['pretty'];
	}

  //constants for the url
	$ENDPOINT_LOGIN="/login";//to login
	$ENDPOINT_REGISTER="/register";//to sign up
	$ENDPOINT_POST="/post";//to add to your joj
	$ENDPOINT_LOGOUT="/logout";//to logout
	$ENDPOINT_PROFILE="/profile";//for the persons details
	$ENDPOINT_NEWSFEED="/newsfeed";//do we need one? or is it better to remain private
	$ENDPOINT_GENERATE="/generate";//to generate the jar or string of images or whatever 




	//query handling
 	$querried=$_GET['url'];
 	switch($querried)
 	{
 		case $ENDPOINT_LOGIN:
 		login();//0
 		break;
 		case $ENDPOINT_REGISTER:
 		signup();//0
 		break;
 		case $ENDPOINT_POST:
 		post();//0
 		break;
 		case $ENDPOINT_LOGOUT:
 		logout();//0
 		break;
 		case $ENDPOINT_PROFILE:
 		profile();
 		break;
 		case $ENDPOINT_NEWSFEED:
 		newsfeed();
 		break; 
 		case $ENDPOINT_GENERATE:
 		generateJar();
 		break; 
		default: 
		$message= array('error'=>'no such endpoint');
		json_print($message);

	}

	//working
	//login implementation
	function login()	
	{	

		global $con;
		$loggedin;
		$username=$_POST['username'];
		$password=$_POST['password'];
		$query = mysqli_query($con,"SELECT * FROM users WHERE 'username'=".$username.";");
		while($row = mysqli_fetch_array($query))
		{
			if($row->password==$password)
			{
				$sessionid=generateUniqueId();
				header("content-type: application/json");
				$data= array('success' => 'true' ,'userid'=>$sessionid );
				
				$quer=mysql_query($con,"INSERT INTO  `a8591040_jarmain`.`active` (`Username` ,`session_id`)VALUES ('".$username".',  '".$sessionid.".');");
			
		}
		if(!$loggedin)
		{
			header("content-type: application/json");
			$data = array('success' => 'false' , 'message'=>'invalid credentials');
			json_print($data);
		}
	}
	
	function logout()
	{
		global $con;
		$username=$_POST['username'];
		$sid=$_POST['userid'];
		$query=mysqli_query($con,"DELETE FROM 'jarmain'.'active' where 'username'= ".$username.';');
		$data= array('success' =>'true','message'=>'successfully logged out' );
	}
 	function signup()
 	{
 		global $con;
 		$username=['username'];
		$query = mysqli_query($con,"SELECT * FROM users WHERE 'username'=".$username.";");
		if(count(mysqli_fetch_array($query))>0)
		{
			$data= array('success'=>'false','message'=>'username already exists');
			json_print($data);
		}
		else
		{
			$password=$_POST['password'];
			$email=$_POST['email'];
			$query=mysqli_query($con,"INSERT INTO  `a8591040_jarmain`.`userdata` (`Email` ,`Username` ,`Password`)VALUES ('".$email".',  '".$username."',  '".$password."');");
			json_print(array('success' => 'true' , 'message' => 'added to the database,welcome.' );)

		}
	}
	function post()
	{
		global $con;
		$userid=$_POST['userid'];
		$username=$_POST['username'];
		if(checkValid($userid,$username))
		{
			$content=$_POST['content'];
			$query=mysqli_query($con,"INSERT INTO  `a8591040_jarmain`.`submissions` (`submission` ,`poster`)VALUES ('".$content."',  '".$username."');");
			$data= array('success' =>'true', 'message'=>'submitted' );
			json_print($data);
		}
	}
	
	function json_print($json_content) {
		global $pretty;
		if($pretty) {
			echo "<pre>";
			echo json_encode($json_content, JSON_PRETTY_PRINT);
			echo "</pre>";
		} else {
			echo json_encode($json_content);
		}


	function generateUniqueId($maxLength = 16) {
    $entropy = '';

    // try ssl first
    if (function_exists('openssl_random_pseudo_bytes')) {
        $entropy = openssl_random_pseudo_bytes(64, $strong);
        // skip ssl since it wasn't using the strong algo
        if($strong !== true) {
            $entropy = '';
        }
    }

    // add some basic mt_rand/uniqid combo
    $entropy .= uniqid(mt_rand(), true);

    // try to read from the windows RNG
    if (class_exists('COM')) {
        try {
            $com = new COM('CAPICOM.Utilities.1');
            $entropy .= base64_decode($com->GetRandom(64, 0));
        } catch (Exception $ex) {
        }
    }

    // try to read from the unix RNG
    if (is_readable('/dev/urandom')) {
        $h = fopen('/dev/urandom', 'rb');
        $entropy .= fread($h, 64);
        fclose($h);
    }

    $hash = hash('whirlpool', $entropy);
    if ($maxLength) {
        return substr($hash, 0, $maxLength);
    }
    return $hash;
}



	function checkvalid($uid,$poster)
	{
		$query=mysql_query($con,"SELECT * FROM   `a8591040_jarmain`.`active` where session_id = ".$uid);
		if(mysql_fetch_array($query)->username==$poster)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
?> 
