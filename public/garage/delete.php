<?php
global $db;
require_once('../../private/initialize.php');
require_login();

if (!isset($_GET['id'])) {
    redirect_to(url_for('/garage/index.php'));
}
$garage_id = $_GET['id'];

$garage = $db->get_garage($garage_id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

if (!is_owner($_SESSION['user_id'], $garage_id)) {
    $_SESSION['error'] = 'You do not have authority to delete that garage';
    redirect_to(url_for('/garage/show.php?id='.h(u($garage['garage_id']))));
}

if (is_post_request()) {
    //TODO Check for last owner, zero items etc otherwise we end up with floating items?
    $db->delete_garage($garage);
    $_SESSION['message'] = 'Garage deleted successfully';
    redirect_to(url_for('/garage/index.php'));
}

$page_title = 'Delete Garage';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>">Back</a>
        </div>

        <p>Are you sure you wish to delete this garage?</p>
        <p class="item"><?php echo h($garage['name']); ?></p>
        <form action="<?php echo url_for('/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>" method="post">
            <div id="operations">
                <button type="submit" class="btn btn-danger">Delete Garage</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>