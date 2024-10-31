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
        ?>
        <div class="centered">
            <a class="btn btn-google" href="<?php echo url_for("auth/google-oauth.php"); ?>" role="button">
                <i class="bi bi-google"></i> Click here to continue with Google
            </a>
            <br><br>
            <a class="btn btn-facebook disabled" href="<?php echo url_for("auth/facebook-oauth.php"); ?>" role="button">
                <i class="bi bi-facebook"></i> Click here to continue with FaceBook
            </a>
        </div>

        <?php
    }
    ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
