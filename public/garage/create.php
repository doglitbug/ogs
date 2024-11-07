<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$locations = $db->get_all_locations();
$garage = [];

if (is_post_request()) {
    $garage['name'] = $_POST['name'] ?? '';
    $garage['description'] = $_POST['description'] ?? '';
    $garage['location_id'] = $_POST['location'] ?? '';
    $garage['visible'] = $_POST['visible'] ?? '';

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

$garage_title = 'Add Garage';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $garage_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/index.php'); ?>">Back</a>
        </div>

        <p>
            Creating a garage is the first step to getting up and running!<br/>
            Please fill out the form below and click 'Create Garage' to continue!
        </p>
        <form action="<?php echo url_for('/garage/create.php'); ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Garage name" aria-label="Garage name" name="name"
                       value="<?php echo h($garage['name']); ?>">
                <?php if (isset($errors['name'])) {
                    echo '<div class="text-danger">' . $errors['name'] . '</div>';
                } ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" placeholder="Garage description" aria-label="Description"
                       name="description"
                       value="<?php echo h($garage['description']); ?>">
                <?php if (isset($errors['description'])) {
                    echo '<div class="text-danger">' . $errors['description'] . '</div>';
                } ?>
            </div>
            <div class="mb-3">
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
            <div class="mb-3">
                <input type="hidden" name="visible" value="0"/>
                <input class="form-check-input" type="checkbox" name="visible" value="1"
                       id="visible" <?php if ($garage['visible'] == 1) echo "checked"; ?>>
                <label class="form-check-label" for="visible">
                    Visible to public?
                </label>
            </div>

            <div id="operations">
                <button type="submit" class="btn btn-success">Create Garage</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>