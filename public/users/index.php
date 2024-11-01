<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$page_title = 'Users';
include(SHARED_PATH . '/public_header.php');
?>

<h1><?php echo $page_title; ?></h1>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
