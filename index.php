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
	$ENDPOINT_NEWSFEED="/newsfeed"//do we need one? or is it better to remain private
	$ENDPOINT_GENERATE="/generate"//to generate the jar or string of images or whatever 




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
				$sessionid=activate($username,$password);
				header("content-type: application/json");
				$data= array('success' => 'true' ,'sessionid'=>$sessionid );
				json_print($data);
				$loggedin=true;
			}
		}
		if(!$loggedin)
		{
			header("content-type: application/json");
			$data = array('success' => 'false' , 'message'=>'invalid credentials');
			json_print($data);
		}
	}
	function activate($username,$password)
	{
		global $con;
		$userid= generateUid();//CREATE THIS FUNCTION
		$query=msqli_query($con,"INSERT INTO 'jarmain'.'active'('userid','username','password') VALUES(".$userid.",".$username.",".$password.";");
	}
	function logout()
	{
		global $con;
		$username=$_POST['username'];
		$sid=$_POST['userid'];
		$query=mysqli_query($con,"DELETE FROM 'jarmain'.'active' where 'username'= ".$username.';') or die(json_print(mysql_error()));
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
			$email=$_POST['email'];gt
			$query=mysqli_query($con,"ALTER TABLE  `submissions` ADD ".$username." TEXT CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL ");
			$query=mysqli_query($con,"INSERT INTO  `a8591040_jarmain`.`userdata` (`Email` ,`Username` ,`Password`)VALUES ('".$email"',  '".$username"',  '".$password"');");
			json_print(array('success' => 'true' , 'message' => 'added to the database,welcome.' );)

		}
	}
	function post()
	{
		global $con;
		$userid=$_POST['userid'];
		$username=$_POST['username'];
		if(checkValid($userid))
		{
			$content=$_POST['content'];
			$query=mysqli_query($con,"INSERT INTO 'jarmain'.'submissions'('.$username.') VALUES(".$content.");");
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

?> 
