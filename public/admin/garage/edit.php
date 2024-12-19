<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$id = $_GET['id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

if (is_post_request()) {
    $garage['name'] = clean_input($_POST['name']);
    $garage['description'] = clean_input($_POST['description'], true);
    $garage['location_id'] = clean_input($_POST['location']);
    $garage['visible'] = clean_input($_POST['visible']);

    $errors = validate_garage($garage);

    if (empty($errors)) {
        $db->update_garage($garage);
        $_SESSION['message'] = 'Garage updated successfully';
        redirect_to(url_for('admin/garage'));
    }
}

$locations = $db->get_locations();

$page_title = 'Edit Garage: ' . h($garage['name']);
include(SHARED_PATH . '/admin_header.php');
?>

    <div id="content">
        <h1>Edit Garage</h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('admin/garage/show.php?id=' . h(u($garage['garage_id']))); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form class="row g-3" method="post">
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Garage name" aria-label="Garage name"
                       name="name"
                       value="<?php echo h($garage['name']); ?>">
                <?php validation('name'); ?>
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
                <?php validation('location'); ?>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea type="text" class="form-control" placeholder="Garage description" aria-label="Description"
                          name="description"
                          rows="5"><?php echo stripcslashes($garage['description']); ?></textarea>
                <?php validation('description'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-warning">Edit Garage</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>