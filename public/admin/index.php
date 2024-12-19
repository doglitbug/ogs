<?php
global $db;
require_once('../../private/initialize.php');
require_admin();

$page_title = 'Admin Area';
include(SHARED_PATH . '/admin_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <p>TODO Settings for Super Admin/Admin to be shown here</p>

</div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>
