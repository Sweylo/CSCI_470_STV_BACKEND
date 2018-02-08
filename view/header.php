<!DOCTYPE html>

<html>
    
<head>
    <title>Chess Champions Management WebUI</title>
    <link rel="stylesheet" type="text/css" href="../view/css/style.css" />
</head>

<body>
	
	<?php if ($me) {
		echo "<p>logged in as: <strong>{$me[user_name]}</strong></p>";
        echo '<p><a href="../user/?action=logout">logout</a></p>';
	} else { ?>
	<form method="post" action="../user/">
		
		<input type="hidden" name="action" value="login">
		
		<div>
            <p class="message error"><?php echo $login_error_message; ?></p>
			<input type="text" name="username" placeholder="username">
			<input type="password" name="password" placeholder="password">
			<input type="submit" value="login">
			<a href="../user/?action=register">create account</a>
		</div>
		
	</form>
	<?php } ?>