<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$id = get_parameter('id');

$garage = $db->get_garage($id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('admin/garage'));
}

if (is_post_request()) {
    //TODO Delete garage
    $_SESSION['message'] = 'Garage deleted successfully';
    redirect_to(url_for('admin/garage'));
}

$page_title = 'Delete Garage: ' . h($garage['name']);
include(SHARED_PATH . '/admin_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/admin/garage'); ?>">
                <i class="bi bi-arrow-left"></i>Back</a>
        </div>
        <div><p>Unfortunately we are unable to delete garages at this time, please consider hiding them<br/>
                This is because we have the following things to consider:
            <ul>
                <li>Garage ownership, what if we have multiple owners, do they have to all agree?</li>
                <li>If deleting a garage, we will need to delete all items, and all images associated in bulk</li>
                <li>Do we back this data up/email it to anyone?</li>
            </ul>
        </div>
    </div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>