<?php 
use ItForFree\SimpleMVC\Config;

$User = Config::getObject('core.user.class');

?>

<?php include "includes/header.php" ?>
<?php 
if( isset($_GET['route'])){
	if($User->userName && 
	   $User->userName != 'guest' && 
	   preg_match('/CMSAdmin/', $_GET['route']) == 1){ 
		  include "includes/adminHeader.php"; 
	}
}

 ?>
	<?= $CONTENT_DATA ?>
<?php include "includes/footer.php" ?>

