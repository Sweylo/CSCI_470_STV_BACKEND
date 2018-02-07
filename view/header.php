<!DOCTYPE html>

<html>
    
<head>
    <title>Chess Champions Management WebUI</title>
    <link rel="stylesheet" type="text/css" href="../view/css/style.css" />
</head>

<body>
	
	<?php if ($me) {
		echo "<p>Welcome, {$me[user_name]}</p>";
	} else { ?>
	<form method="post" action="../user">
		
		<input type="hidden" name="action" value="login">
		
		<div>
			<input type="text" name="username" placeholder="username">
			<input type="password" name="password" placeholder="password">
			<input type="submit" value="login">
			<a href="../user/?action=register">create account</a>
		</div>
		
	</form>
	<?php } ?>