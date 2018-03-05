<?php require_once($dir_depth . 'model/user_db.php'); ?>

<!DOCTYPE html>

<html>
    
<head>
    <title><?php echo $webui_config['site_title']; ?></title>
    <link rel="stylesheet" type="text/css" href="../view/default/style.css" />
</head>

<body>
	
	<header>
		
		<h1><?php echo $webui_config['site_title']; ?></h1>
	
		<?php 

		if ($me) {
			echo "<p>logged in as: <strong>{$me['user_name']}</strong> - ";
			echo '<a href="../login/?action=logout">logout</a></p>';
			echo '<hr />';
		} 

		?>

	</header>