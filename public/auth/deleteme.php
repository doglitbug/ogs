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
    //TODO Close $db?
    redirect_to(url_for('auth/profile.php'));
}

$users = $db->get_all_users();
?>

<form action="<?php echo url_for('/auth/deleteme.php'); ?>" method="post">
    <select name="email">
        <?php
        foreach ($users as $user) {
            echo '<option value="' . $user['email'] . '">' . $user['username'] . ' - ' . $user['access'] . '</option>';
        }
        ?>
    </select>
    <input type="submit" value="Log in"/>
</form>