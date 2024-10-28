<?php
global $db;
require_once('../../private/initialize.php');
//TODO Check if already logged in, ask if want to log out?

$page_title = 'Log in';
include(SHARED_PATH . '/public_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
