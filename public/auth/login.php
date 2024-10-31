<?php
global $db;
require_once('../../private/initialize.php');
//TODO Check if already logged in, ask if want to log out?

$page_title = 'Log in';
include(SHARED_PATH . '/public_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php if (is_logged_in()) {
        echo 'You are already logged in!<br>';
    } else {
        echo '<p>Please click <a href="' . url_for("auth/google-oauth.php") . '">here</a> to log in with Google</p>';
    }
    ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
