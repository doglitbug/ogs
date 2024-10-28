<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$page_title = 'Garages';
include(SHARED_PATH . '/public_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
