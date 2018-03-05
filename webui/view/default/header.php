<?php 

require_once($dir_depth . 'model/user_db.php'); 
require_once('../view/html.php');

$actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
echo $actual_link;

?>

<!DOCTYPE html>

<html>
    
<head>
    <title><?php echo $webui_config['site_title']; ?></title>
    <link rel="stylesheet" type="text/css" href="../view/default/style.css" />
</head>

<body>
	
	<header>
	
		<?php 
		
		echo html('h1', [], $webui_config['site_title']);

		if ($me) {
			echo html('p', ['class' => 'welcome'], [
				'logged in as: ', html('strong', [], $me['user_name']),' - ', 
				html('a', ['href' => '../login/?action=logout'], 'logout')
			]);
		} 
		
		$breadcrumbs = [
			html('li', [], html('a', ['href' => '../main/'], '&#8962;Home'))
		];
		
		echo html('ul', ['id' => 'breadcrumbs'], $breadcrumbs);
		
		?>

	</header>
	
	<hr />