<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$item = [];
$id = $_GET['garage_id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

if (!is_owner_or_worker($garage)) {
    $_SESSION['error'] = 'You do not have authority to add items to that Garage';
    redirect_to(url_for('/garage/show.php?id=' . h(u($garage['garage_id']))));
}

if (is_post_request()) {
    $item['garage_id'] = $garage['garage_id'];
    $item['name'] = clean_input($_POST['name'], []) ?? '';
    $item['description'] = clean_input($_POST['description']) ?? '';
    $item['visible'] = $_POST['visible'] ?? '';

    $errors = validate_item($item, $_FILES);
    if (empty($errors)) {
        $item_id = $db->insert_item($item);
        move_and_link_images($_FILES, $item_id);

        $_SESSION['message'] = 'Item created successfully';
        redirect_to(url_for('/item/show.php?id=' . $item_id));
    }
} else {
    $item['name'] = '';
    $item['description'] = '';
    $item['visible'] = '1';
}

$page_title = 'Add item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form action="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>" method="post"
              enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl-6">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" placeholder="Item name" aria-label="Item name" name="name"
                           value="<?php echo h($item['name']); ?>">
                    <?php if (isset($errors['name'])) {
                        echo '<div class="text-danger">' . $errors['name'] . '</div>';
                    } ?>
                </div>
                <div class="col-xl-6">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" placeholder="Item description" aria-label="Description"
                              name="description"
                              rows="5"><?php echo $item['description']; ?></textarea>
                    <?php if (isset($errors['description'])) {
                        echo '<div class="text-danger">' . $errors['description'] . '</div>';
                    } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="form-check form-switch">
                        <input type="hidden" name="visible" value="0"/>
                        <input class="form-check-input" type="checkbox" name="visible" value="1"
                               id="visible" <?php if ($item['visible'] == 1) echo "checked"; ?>>
                        <label class="form-check-label" for="visible">
                            Visible to public?
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <h3>Images</h3>
                <div class="col-xl-6">
                    <label for="images" class="form-label">Add image</label>
                    <input type="file" id="images" name="images">
                    <?php if (isset($errors['images'])) {
                        echo '<div class="text-danger">' . $errors['images'] . '</div>';
                    } ?>
                </div>
            </div>
            <div class="row">
                <div id="operations">
                    <button type="submit" class="btn btn-warning">Add Item</button>
                </div>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>