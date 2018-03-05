<!DOCTYPE html>

<html>

	<head>
		<title>Chess &amp; Conquer Admin WebUI - Login</title>
	</head>

	<body>
		
		<h1>Chess &amp; Conquer Admin WebUI - Login</h1>
		
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

	</body>

</html>