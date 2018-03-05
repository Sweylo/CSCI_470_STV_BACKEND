<?php include('../view/login/header.php'); ?>

<h2>Login</h2>
		
<form method="post" action="./?action=validate_user">

	<div>Username&nbsp;<input type="text" name="username"></div>
	<div>Password&nbsp;<input type="password" name="password"></div>

	<br />

	<div>
		<input type="submit" value="Login"> 
		<span>&nbsp;or&nbsp;</span> 
		<a href="./?action=register">Register</a>
	</div>

</form>

<?php include('../view/login/footer.php'); ?>