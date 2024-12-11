<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$id = get_parameter('id');

$user = $db->get_user($id);
if ($user == null) {
    $_SESSION['error'] = 'User not found';
    redirect_to(url_for('/staff/user'));
}

if (is_post_request()) {
    //TODO Delete user
    $_SESSION['message'] = 'User deleted successfully';
    redirect_to(url_for('staff/user'));
}

$page_title = 'Delete User: ' . h($user['username']);
include(SHARED_PATH . '/staff_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/staff/user'); ?>">
                <i class="bi bi-arrow-left"></i>Back</a>
        </div>
        <div><p>Unfortunately we are unable to delete users at this time, please consider locking them out<br/>
            This is because we have the following things to consider:
            <ul>
                <li>Garage ownership, do we delete all singularly owned garages for this user?</li>
                <li>If deleting a garage, we will need to delete all items, and all images associated in bulk</li>
                <li>Do we back this data up/email it to the address?</li>
            </ul>
        </div>
    </div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>