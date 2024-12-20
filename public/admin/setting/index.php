<?php
global $db, $settings;
require_once('../../../private/initialize.php');
require_admin();

$page_title = 'Admins settings';
include(SHARED_PATH . '/admin_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php dump($settings); ?>
</div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>
