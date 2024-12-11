<?php
global $db;
require_once('../../private/initialize.php');
require_admin();

$page_title = 'Staff Area';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <p>TODO Settings for Superadmin/admin to be shown here</p>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
