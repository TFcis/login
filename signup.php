<?php
require_once("system.php");
require_once("func/cURL-HTTP-function/curl.php");

$signupfail=false;
$E["POST"]=$_POST;
if(isset($_POST["account"])){
	if($_POST["account"]==""){
		$signupfail=true;
		$E["msg"].=_("Account")." "._("is empty.")." ";
	}
	if($_POST["password"]==""){
		$signupfail=true;
		$E["msg"].=_("Password")." "._("is empty.")." ";
	}else if($_POST["password2"]==""){
		$signupfail=true;
		$E["msg"].=_("Confirm")." "._("is empty.")." ";
	}else if($_POST["password"]!==$_POST["password2"]){
		$signupfail=true;
		$E["msg"].=_("password_diff")." ";
	}
	if($_POST["nickname"]==""){
		$signupfail=true;
		$E["msg"].=_("Nickname")." "._("is empty.")." ";
	}
	if($_POST["realname"]==""){
		$signupfail=true;
		$E["msg"].=_("Realname")." "._("is empty.")." ";
	}
	if($_POST["email"]==""){
		$signupfail=true;
		$E["msg"].=_("Email")." "._("is empty.")." ";
	}
	$response=cURL_HTTP_Request('https://www.google.com/recaptcha/api/siteverify',array('secret'=>$config['reCAPTCHA']['secret_key'],'response'=>$_POST['g-recaptcha-response']));
	if(!json_decode($response->html)->success){
		$signupfail=true;
		$E["msg"] = _("reCAPTCHA_fail");
	}
	if(!$signupfail){
		$checkaccount = checkPassword($_POST["account"]);
		$checkemail = checkPassword($_POST["email"]);
		if($checkaccount === -1 && $checkemail === -1){
			try{
				$db = PDO_prepare("INSERT INTO `table:account` (`account`, `password`, `email`, `nickname`, `realname`) VALUES (:account, :password, :email, :nickname, :realname)");
				$db->bindValue("account", $_POST["account"]);
				$db->bindValue("password", password_hash($_POST["password"], PASSWORD_DEFAULT));
				$db->bindValue("email", $_POST["email"]);
				$db->bindValue("nickname", $_POST["nickname"]);
				$db->bindValue("realname", $_POST["realname"]);
				$db->execute();
			}catch(PDOException $e){
				die("SQL ERROR: " . $e->getMessage());
			}

			$E["msg"] = "Signup succeed. Please login.";
			require("template/blank.php");
			header('refresh: 3;url=login.php');
		}else {
			if($checkaccount !== -1){
				$E["msg"].="Account is exist. ";
				$E["POST"]["account"]="";
			}
			if($checkemail !== -1){
				$E["msg"].="Email is exist. ";
				$E["POST"]["email"]="";
			}
			require("template/signup.php");
		}
	}else{
		require("template/signup.php");
	}
}else{
	require("template/signup.php");
}
