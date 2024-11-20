<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$id = $_GET['id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

if (!is_owner($id)) {
    //TODO Check for last owner?
    $_SESSION['error'] = 'You do not have authority to delete that garage';
    redirect_to(url_for('/garage/show.php?id=' . h(u($garage['garage_id']))));
}

if (is_post_request()) {
    $item_count = count($db->get_all_items(['garage_id' => $garage['garage_id']]));
    if ($item_count != 0) {
        $_SESSION['error'] = 'Garage must be empty to delete';
    } else {
        $db->delete_garage($garage);
        $_SESSION['message'] = 'Garage deleted successfully';
        redirect_to(url_for('/garage/index.php'));
    }
}

$page_title = 'Delete Garage: ' . h($garage['name']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>"><i class="bi bi-arrow-left"></i>Back</a>
        </div>

        <p>Are you sure you wish to delete this garage?</p>
        <form action="<?php echo url_for('/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>" method="post">
            <div id="operations">
                <button type="submit" class="btn btn-danger">Delete Garage</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>