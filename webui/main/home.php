<?php require('../view/default/header.php'); ?>

<h2>Home</h2>

<?php if ($me['user_account_type_id'] > USER_TYPE_USER) { ?>
<p><a href="../user/?action=list_users">list users</a></p>
<p><a href="../board/?action=board_designer">board designer - under construction</a></p>
<?php } ?>
<p><a href="../match/?action=list_matches&limit=10">list matches</a></p>