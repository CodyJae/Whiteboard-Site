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
	
	$username = mysqli_real_escape_string($request->username);
	$password = mysqli_real_escape_string($request->password);
	
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
	mysql_connect("localhost","user","password") or die(mysql_error());
	mysql_select_db("whiteboard");
	$vals = mysql_query("select * from users where user = '$name';");
	if(mysql_num_rows($vals) != 1)
	{
		http_response_code(401);
		$result['reason'] = "The credentials do not match";
		return $result;
	}
	if(mysql_num_rows($vals) == 1)
	{
		$row = mysql_fetch_array($vals);
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
