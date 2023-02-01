<?php
//return login userid
function checklogin($cookie=null){
	global $config;
	if(is_null($cookie))
		$cookie = @$_COOKIE["login"];
	if(!isset($cookie))
		return false;
	//clear expired db data
	try{
		$db = PDO_prepare("DELETE FROM `table:session` WHERE `expire`<CURDATE()");
		$db->execute();
		//check cookie
		$db = PDO_prepare("SELECT `id` FROM `table:session` WHERE `cookie`=:cookie");
		$db->bindValue("cookie", $cookie, PDO::PARAM_STR);
		$db->execute();
		if($db->rowCount()>0){
			$uid = $db->fetchAll()[0]["id"];
			return $uid;
		}else{
			//delete cookie
			setcookie("login",null,-1,$config["session"]["cookie_path"],$config["session"]["cookie_domain"],false,true);
			return false;
		}
	}catch(PDOException $e){
		die("SQL ERROR: " . $e->getMessage());
	}
}

function checkPassword($account,$password="") //return value: success: user, no such acct: -1, wrong pass: -2
{
	$db = PDO_prepare("SELECT * FROM `table:account` WHERE `account`=? OR `email`=?");
	try{
		$db->bindValue(1, $account, PDO::PARAM_STR);
		$db->bindValue(2, $account, PDO::PARAM_STR);
		$db->execute();
		if($db->rowCount()<1)
			return -1;
		$data = $db->fetchAll()[0];
		if(!password_verify($password, $data['password']))
			return -2;
		return $data;
	}catch(PDOException $e){
		die("SQL ERROR: " . $e->getMessage());
	}
}

function checkURL($url){
	global $config;
	$domain = parse_url($url,PHP_URL_HOST);
	if($domain===NULL)return true;
	foreach ($config["site"]["available_domain"] as $d) {
		if(preg_match($d,$domain)===1)
			return true;
	}
	return false;
}
