<?php require('../view/header.php'); ?>

<p><a href='../'>&LT;&LT; back to main</a></p>

<h2>Registration</h2>

<p class="message <?php echo $is_error_message ? 'error' : 'info';  ?>"><?php echo $message; ?></p>

<form method="post" action="./">
    
    <input type="hidden" name="action" value="add_user">
    
    <div>
		<label>Username</label>
		<input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>">
	</div>
	<div>
		<label>E-mail</label>
		<input type="text" name="email" placeholder="Email address" value="<?php echo $email; ?>">
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
    
    <br />
	
	<input class="button start" type="submit" value="Save">
    
</form>

<?php //include '../view/footer.php'; ?>