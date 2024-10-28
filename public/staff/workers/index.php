<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$page_title = 'Workers';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
