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

if (!is_owner($garage['garage_id'])) {
    $_SESSION['error'] = 'You do not have authority to edit that garage';
    redirect_to(url_for('/garage/index.php'));
}

if (is_post_request()) {
    $garage['garage_id'] = $garage['garage_id'];
    $garage['name'] = clean_input($_POST['name'], []) ?? '';
    $garage['description'] = clean_input($_POST['description']) ?? '';
    $garage['location_id'] = $_POST['location'] ?? '';
    $garage['visible'] = $_POST['visible'] ?? '';

    $errors = validate_garage($garage);

    if (empty($errors)) {
        $db->update_garage($garage);
        $_SESSION['message'] = 'Garage updated successfully';
        redirect_to(url_for('/garage/show.php?id=' . $garage['garage_id']));
    }
}

$locations = $db->get_all_locations();

$page_title = 'Edit Garage: ' . h($garage['name']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1>Edit Garage</h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form action="<?php echo url_for('/garage/edit.php?id=' . h(u($garage['garage_id']))); ?>" method="post">
            <div class="row">
                <div class="col-xl-4">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" placeholder="Garage name" aria-label="Garage name"
                           name="name"
                           value="<?php echo h($garage['name']); ?>">
                    <?php if (isset($errors['name'])) {
                        echo '<div class="text-danger">' . $errors['name'] . '</div>';
                    } ?>
                </div>
                <div class="col-xl-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="visible" value="0"/>
                        <input class="form-check-input" type="checkbox" name="visible" value="1"
                               id="visible" <?php if ($garage['visible'] == 1) echo "checked"; ?>>
                        <label class="form-check-label" for="visible">
                            Visible to public?
                        </label>
                    </div>
                </div>
                <div class="col-xl-4">
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
            </div>
            <div>
                <label for="description" class="form-label">Description</label>
                <textarea type="text" class="form-control" placeholder="Garage description" aria-label="Description"
                          name="description"
                          rows="5"><?php echo $garage['description']; ?></textarea>
                <?php if (isset($errors['description'])) {
                    echo '<div class="text-danger">' . $errors['description'] . '</div>';
                } ?>
            </div>
            <div class="row">
                <div id="operations">
                    <button type="submit" class="btn btn-warning">Edit Garage</button>
                </div>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>