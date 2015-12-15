<?php
	include('JWT.php');
function handleLogin()
{
	$private_key = "2dQI1Dlvam2SA6acXpwdmJO0";
	
	$postdata = file_get_contents("php://input");
	$request = json_decode($postdata);
	if(!$request)
	{
		http_response_code(400);
		$result['reason'] = "invalid request";
		return $result;
	}
		
	$result = array();
	$dbcon = mysqli_connect("localhost","user","password") or die(mysql_error());
	$d = mysqli_select_db($dbcon,"whiteboard");
	
	$username = mysqli_real_escape_string($d, $request->username);
	$password = mysqli_real_escape_string($d, $request->password);
	
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
	$vals = mysqli_query("select * from users where user = '$username';");
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
		'ID' => $result->userid,
		'username' => $result->username,
		'iat' => time()
	);
	$jwt = JWT::encode($_jwt_data, $private_key);
	
	$result['token'] = $jwt;
	$result['username'] = $result->username;
	return $result;
}

$result = handleLogin();
header('Content-type:application/json;charset=utf-8');
echo json_encode($result);
?>
