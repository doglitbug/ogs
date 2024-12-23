<?php
global $db, $settings;
require_once('../../private/initialize.php');
require_login();

$garage = [];

$current_garages = sizeof($db->get_garages_by_user($_SESSION['user_id'], ['access' => 'Owner']));
$max_garages = $settings->get('max_garages');

if ($current_garages >= $max_garages) {
    $_SESSION['error'] = 'Unable to create additional Garages as you have reached or are over your allocation of ' . $max_garages . ' per account';
    redirect_to(url_for('/garage/'));
}

if (is_post_request()) {
    $garage['name'] = clean_input($_POST['name']);
    $garage['description'] = clean_input($_POST['description'], true);
    $garage['location_id'] = clean_input($_POST['location']);
    $garage['visible'] = clean_input($_POST['visible']);

    $errors = validate_garage($garage);

    if (empty($errors)) {
        $new_id = $db->insert_garage($garage);
        $db->set_user_garage_access($_SESSION['user_id'], $new_id, "Owner");
        $_SESSION['message'] = 'Garage created successfully';
        redirect_to(url_for('/garage/show.php?id=' . $new_id));
    }
} else {
    $garage['name'] = '';
    $garage['description'] = '';
    $garage['location_id'] = $_SESSION['location_id'];
    $garage['visible'] = '1';
}

$locations = $db->get_locations();

$garage_title = 'New Garage';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $garage_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/index.php'); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
        </div>

        <p>
            Creating a garage is the first step to getting up and running!<br/>
            Please fill out the form below and click 'Create Garage' to continue!
        </p>
        <form class="row g-3" action="<?php echo url_for('/garage/create.php'); ?>" method="post">
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Garage name" aria-label="Garage name"
                       name="name"
                       value="<?php echo h($garage['name']); ?>">
                <?php validation('name'); ?>
            </div>
            <div class="col-md-6">
                <label for="location" class="form-label">Location</label>
                <select class="form-select" name="location" aria-label="Location">
                    <?php
                    foreach ($locations as $location) {
                        echo "<option value=\"{$location['location_id']}\"";
                        if ($garage['location_id'] == $location['location_id']) {
                            echo " selected";
                        }
                        echo ">{$location['description']}</option>";
                    }
                    ?>
                </select>
                <?php if (isset($errors['location'])) {
                    echo '<div class="text-danger">' . $errors['location'] . '</div>';
                } ?>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="visible" value="0"/>
                    <input class="form-check-input" type="checkbox" name="visible" value="1"
                           id="visible" <?php if ($garage['visible'] == 1) echo "checked"; ?>>
                    <label class="form-check-label" for="visible">
                        Visible to public?
                    </label>
                </div>
                <?php validation('visible'); ?>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" placeholder="Garage description" aria-label="Description"
                          name="description"
                          rows="5"><?php echo stripcslashes($garage['description']); ?></textarea>
                <?php validation('description'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-success">Create Garage</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>