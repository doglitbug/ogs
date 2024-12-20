<?php
global $db, $settings;
require_once('../../private/initialize.php');
require_login();

$id = $_GET['id'] ?? '0';

$item = $db->get_item($id);
if ($item == null) {
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

if (!can_edit_item($item)) {
    $_SESSION['error'] = 'You do not have authority to edit that item';
    redirect_to(url_for('/item/show.php?id=' . h(u($item['item_id']))));
}

$images = $db->get_item_images($item['item_id']);
$max_images = $settings->get('max_images');

if (is_post_request()) {
    //Check to see if POST data was discarded due to overlarge file
    if (count($_POST) != 0) {
        //garage_id pulled from database!
        $item['name'] = clean_input($_POST['name']);
        $item['description'] = clean_input($_POST['description'], true);
        $item['visible'] = clean_input($_POST['visible']);

        $errors = validate_item($item, $_FILES);


        if (empty($errors)) {
            $db->update_item($item);

            //Remove deleted images
            if (isset($_POST['delete'])) {
                foreach ($_POST['delete'] as $image_id) {
                    $image = $db->get_image($image_id);
                    //Check it exists and belongs to item (no form tampering!)
                    if (!$image || !in_array($image_id, array_column($images, 'image_id'))) break;
                    unlink(PUBLIC_PATH . '/images/' . $image['source']);
                    //Remove from images (will cascade to item_image)
                    $db->delete_image($image);
                }
            }

            //Now that we (may) have removed images, get new count and check limit
            //TODO Add error if attempting to go over the limit
            $current_images = sizeof($db->get_item_images($item['item_id']));
            if ($current_images < $settings->get('max_images')) {
                //Add new images
                //TODO This assumes that we only ever add ONE image here, if not we might go over the limit
                //Remember that $_FILES might not always have valid image data etc
                move_and_link_images($_FILES, $item['item_id']);
            }
            $_SESSION['message'] = 'Item updated successfully';
            redirect_to(url_for('/item/show.php?id=' . h(u($item['item_id']))));
        }
    } else {
        $_SESSION['error'] = "File upload too large";
    }
}

$page_title = 'Edit Item: ' . h($item['name']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/item/show.php?id=' . h(u($item['item_id']))); ?>">
                <i class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form class="row g-3" action="<?php echo url_for('/item/edit.php?id=' . h(u($item['item_id']))); ?>"
              method="post"
              enctype="multipart/form-data">
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Item name" aria-label="Item name" name="name"
                       value="<?php echo h($item['name']); ?>">
                <?php validation('name'); ?>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="visible" value="0"/>
                    <input class="form-check-input" type="checkbox" name="visible" value="1"
                           id="visible" <?php if ($item['visible'] == 1) echo "checked"; ?>>
                    <label class="form-check-label" for="visible">
                        Visible to public?
                    </label>
                </div>
                <?php validation('visible'); ?>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" placeholder="Item description" aria-label="Description"
                          name="description"
                          rows="5"><?php echo stripcslashes($item['description']); ?></textarea>
                <?php validation('description'); ?>
            </div>

            <h3>Images: <?php echo sizeof($images) . '/' . $max_images; ?></h3>

            <?php foreach ($images as $image) {
                list($width, $height) = rescale_image_size($image);
                $id = $image['image_id'];
                ?>
                <div class="col-md-6 col-xl-3">
                    <a href="<?php echo url_for('image/show.php?id=' . h(u($id))); ?>">
                        <img src="<?php echo url_for('images/' . $image['source']); ?>"
                             width="<?php echo $width; ?>"
                             height="<?php echo $height; ?>">
                    </a>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="delete[]"
                               id="del_<?php echo u($id); ?>"
                               value="<?php echo u($id); ?>"
                            <?php if (isset($_POST['delete']) && in_array($id, $_POST['delete'])) echo 'checked'; ?> >
                        <label class="form-check-label" for="del_<?php echo u($id); ?>">
                            Delete?
                        </label>
                    </div>
                </div>
            <?php } ?>

            <h3>Add Image:</h3>
            <div class="col-12">
                <input type="file" id="images" name="images">
                <?php validation('images'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-warning">Edit Item</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>