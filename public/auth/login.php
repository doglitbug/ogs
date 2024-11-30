<?php
global $db;
require_once('../../private/initialize.php');

if ($_ENV['APPLICATION_ENV'] !== "DEV") {
    die("This should not even exist in PROD");
}

if (is_post_request()) {
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['logon_method'] = "Impersonation";
    log_in();
    redirect_to(url_for('user/show.php'));
}

$users = $db->get_all_users();

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
                <a class="btn btn-social btn-google" href="<?php echo url_for("auth/google-oauth.php"); ?>"
                   role="button">
                    <i class="bi bi-google"></i> Log in with Google
                </a>
                <br>
                <a class="btn btn-social btn-facebook disabled" href="<?php echo url_for("auth/facebook-oauth.php"); ?>"
                   role="button">
                    <i class="bi bi-facebook"></i> Log in with FaceBook
                </a>
            </div>
            <br/>
            <br/>
            <div class="centered">
                <h1>Impersonation</h1>
                <form action="<?php echo url_for('/auth/login.php'); ?>" method="post">
                    <select name="email">
                        <?php
                        foreach ($users as $user) {
                            echo '<option value="' . $user['email'] . '">' . $user['username'] . ' - ' . $user['access'] . '</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-secondary">Impersonate</button>
                </form>
            </div>
        <?php } ?>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>