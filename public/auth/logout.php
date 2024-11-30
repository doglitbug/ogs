<?php
global $db;
require_once('../../private/initialize.php');
require_login();
log_out();

$page_title = 'Log out';
include(SHARED_PATH . '/public_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <p>You are now logged out, please click <a href="<?php echo url_for('auth/login.php'); ?>">here</a> to log back in
    </p>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
