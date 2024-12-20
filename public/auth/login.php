<?php
global $db;
require_once('../../private/initialize.php');

if (is_post_request() && $_ENV['APPLICATION_ENV'] == "DEV") {
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['logon_method'] = "Impersonation";
    log_in();
    redirect_to(url_for('/'));
}

$users = $db->get_users();

$page_title = 'Log in:';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <?php if (is_logged_in()) {
            echo 'You are already logged in!<br>';
        } else {
            ?>
            <div class="centered">
                <br>
                <?php if (isset($_ENV['google_oauth_client_id'])) { ?>
                    <a class="btn btn-social btn-google"
                       href="<?php echo url_for("auth/google-oauth.php"); ?>"
                       role="button">
                        <i class="bi bi-google"></i> Continue with Google
                    </a>
                <?php } ?>
                <br>
                <?php if (isset($_ENV['facebook_oauth_app_id'])) { ?>
                    <a class="btn btn-social btn-facebook"
                       href="<?php echo url_for("auth/facebook-oauth.php"); ?>"
                       role="button">
                        <i class="bi bi-facebook"></i> Continue with FaceBook
                    </a>
                <?php } ?>
            </div>
            <br/>

            <?php if ($_ENV['APPLICATION_ENV'] == "DEV") { ?>
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

        <?php } ?>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>