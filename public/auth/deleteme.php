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
    redirect_to(url_for('auth/profile.php'));
}

$users = $db->get_all_users();
$page_title = "Impersonation";
include(SHARED_PATH . '/public_header.php');
?>
    <h1><?php echo $page_title; ?></h1>
    <div id="content" class="centered">
        <form action="<?php echo url_for('/auth/deleteme.php'); ?>" method="post">
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
<?php include(SHARED_PATH . '/public_footer.php'); ?>