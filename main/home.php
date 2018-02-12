<?php require('../view/header.php'); ?>

<h2>Chess Champions Management WebUI</h2>

<?php if ($me['user_account_type_id'] > 2) { ?>
<p><a href="../user/?action=list_users">list users</a></p>
<p><a href="../board/?action=board_designer">board designer</a></p>
<?php } ?>
<p><a href="../match/?action=list_matches&limit=10">list matches</a></p>