<?php
if(!defined("IN_SYSTEM"))
	die("Access Denied");
?>

<?php require("header.php"); ?>

<center>
<form method="POST">
	Account: <?=@$E["info"]["account"]?><br>
	Password: <input type="password" name="password"><br>
	Confirm: <input type="password" name="password2"><br>
	Nickname: <input type="text" name="nickname" value="<?=@$E["info"]["nickname"]?>"><br> 
	E-mail: <input type="email" name="email" value="<?=@$E["info"]["email"]?>"><br>
	Facebook: <input type="hidden" id="facebook" name="facebook" value="">
	<?php
		if(@$E["info"]["fbid"]==""){
			?>
				<a href="<?php echo getloginurl($config["site"]["url"].'setting.php'); ?>">connect facebook</a>
			<?php
		}else {
			echo @$E["info"]["fbid"];
			?>
				<button type="submit" onclick="facebook.value='unconnect';">unconnect</button>
			<?php
		}
	?>
	<br>
	<input type="submit" value="edit">
</form>
</center>

<?php require("footer.php"); ?>
