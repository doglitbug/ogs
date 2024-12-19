<?php
global $db;
require_once('../../../private/initialize.php');
require_super_admin();

$page_title = 'Admins';
include(SHARED_PATH . '/admin_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>
