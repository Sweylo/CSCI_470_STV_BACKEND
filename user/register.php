<?php //require('../view/header.php'); ?>

<h2>Registration</h2>

<form method="post" action="./">
    
    <input type="hidden" name="action" value="add_user">
    
    <div>
		<label>Username</label>
		<input type="text" name="username" placeholder="Username">
	</div>
	<div>
		<label>E-mail</label>
		<input type="text" name="email" placeholder="Email address">
	</div>
	
	<br />
	
	<div>
		<label>Password</label>
		<input type="password" name="password" placeholder="Password">
	</div>
	<div>
		<label>Confirm password</label>
		<input type="password" name="confirm" placeholder="Confirm">
	</div>
	
	<input class="button start" type="submit" value="Save">
    
</form>

<?php //include '../view/footer.php'; ?>