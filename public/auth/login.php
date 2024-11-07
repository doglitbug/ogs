<?php
global $db;
require_once('../../private/initialize.php');

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
            <a class="btn btn-social btn-google" href="<?php echo url_for("auth/google-oauth.php"); ?>" role="button">
                <i class="bi bi-google"></i> Log in with Google
            </a>
            <br>
            <a class="btn btn-social btn-facebook disabled" href="<?php echo url_for("auth/facebook-oauth.php"); ?>" role="button">
                <i class="bi bi-facebook"></i> Log in with FaceBook
            </a>
        </div>

        <?php
    }
    ?>
</div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>
