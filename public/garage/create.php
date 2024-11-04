<?php
global $db;
require_once('../../private/initialize.php');
require_login();

//$locations = $db->get_all_locations();
$garage = [];

if (is_post_request()) {
    $garage['name'] = $_POST['name'] ?? '';
    $garage['description'] = $_POST['description'] ?? '';
    $garage['location_id'] = $_POST['location_id'] ?? '';
    $garage['visible'] = $_POST['visible'] ?? '';

    $errors = validate_garage($garage);
    if (empty($errors)) {
        $new_id = $db->insert_garage($garage);
        //TODO Set this user as the owner!

        $_SESSION['message'] = 'Garage created successfully';
        redirect_to(url_for('/garages/show.php?id=' . $new_id));
    }
} else {
    $garage['name'] = '';
    $garage['description'] = '';
    $garage['location_id'] = $_SESSION['location_id'];
    $garage['visible'] = '1';
}

$garage_title = 'Add Garage';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $garage_title; ?></h1>

        <form action="<?php echo url_for('/garage/create.php'); ?>" method="post">
            
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>