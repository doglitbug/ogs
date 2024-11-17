<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$id = $_GET['id'] ?? '1';

$item = $db->get_item($id);
if ($item == null) {
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

if (!can_edit_item($item)) {
    $_SESSION['error'] = 'You do not have authority to edit that item';
    redirect_to(url_for('/item/show.php?id=' . h(u($item['item_id']))));
}

if (is_post_request()) {
    //garage_id pulled from database!
    $item['name'] = $_POST['name'] ?? '';
    $item['description'] = $_POST['description'] ?? '';
    $item['visible'] = $_POST['visible'] ?? '';

    $errors = validate_item($item, $_FILES);

    if (empty($errors)) {
        $db->update_item($item);
        move_and_link_images($_FILES, $item['item_id']);

        $_SESSION['message'] = 'Item updated successfully';
        redirect_to(url_for('/item/show.php?id=' . h(u($item['item_id']))));
    }
}

$images = $db->get_item_images($item['item_id']);

$page_title = 'Edit Item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/item/show.php?id=' . h(u($item['item_id']))); ?>">Back</a>
        </div>

        <form action="<?php echo url_for('/item/edit.php?id=' . h(u($item['item_id']))); ?>" method="post"
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
                    <input type="text" class="form-control" placeholder="Item description" aria-label="Description"
                           name="description"
                           value="<?php echo h($item['description']); ?>">
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
                <h3>Images:</h3>
                <div class="images">
                    <?php foreach ($images as $image) {
                        list($width, $height) = rescale_image($image);

                        echo '<a href="' . url_for('image/show.php?id=' . h(u($image['image_id']))) . '">';
                        echo '<img src="' . url_for('images/' . $image['source']) . '" width="' . $width . '" height="' . $height . '">';
                        echo '</a>';
                    } ?>
                </div>
            </div>
            <div class="row">
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
                    <button type="submit" class="btn btn-warning">Edit Item</button>
                </div>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>