<?php
include('JWT.php');

$dbcon = mysqli_connect("localhost","user","password","whiteboard");
if(!$dbcon) {
  http_response_code(400);
  echo "Error: Unable to connect to MySQL." . PHP_EOL;
  echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
  echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
  exit;
}


function handleLogin()
{
  global $dbcon;
  
	$private_key = "2dQI1Dlvam2SA6acXpwdmJO0";
  	$result = array();
	
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	$result["post"] = $request;
  
	if(!$request)
	{
		http_response_code(400);
		$result['reason'] = "invalid request";
		return $result;
	}
  
	$username = mysqli_real_escape_string($dbcon, $request->username);
	$password = mysqli_real_escape_string($dbcon, $request->password);
	
	if(!($username && is_string($username) && strlen($username) > 0))
	{
		http_response_code(400);
		$result['reason'] = "Invalid username";
		return $result;	
	}	
	if(!($password && is_string($password) && strlen($password) > 0))
	{
		http_response_code(400);
		$result['reason'] = "invalid password";
		return $result;
	}
  //SCOTT is this just another way of writting mysqli_real_escape_string????
  //no, the user could still do something like -- to comment out the semicolon.
	$vals = mysqli_query($dbcon, "select * from users where user = '$username';");
	if(mysqli_num_rows($vals) != 1)
	{
		http_response_code(401);
		$result['reason'] = "The credentials do not match";
		return $result;
	}
	if(mysqli_num_rows($vals) == 1)
	{
		$row = mysqli_fetch_array($vals);
		$pass = $row['pass']; 
		$user = $row['user'];
		$id = $row['user_id'];
	}
	$_jwt_data = array(
		'ID' => $id,
		'username' => $user,
		'iat' => time()
	);
	$jwt = JWT::encode($_jwt_data, $private_key);
	
	$result['token'] = $jwt;
	$result['username'] = $user;
	return $result;
}

$result = handleLogin();

header('Content-type:application/json;charset=utf-8');
echo json_encode($result);
?>
