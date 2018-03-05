<?php require('../view/default/header.php'); ?>

<h2>List Users</h2>

<table>
	
	<tr>
		<th>id</th>
		<th>username</th>
		<th>email</th>
	</tr>
	
	<?php foreach ($users as $user) { ?>
	<tr>
		<td><?php echo $user['user_id']; ?></td>
		<td><?php echo $user['user_name']; ?></td>
		<td><?php echo $user['user_email']; ?></td>
	</tr>
	<?php } ?>
	
</table>