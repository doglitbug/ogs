<?php
global $db, $settings;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '0';

$item = $db->get_item($id, ["public" => true]);
if ($item == null || ($item['visible'] == '0' && !can_edit_item($item))) {
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

$images = $db->get_item_images($item['item_id']);
$max_images = $settings->get('max_images');

$page_title = 'Show Item: ' . $item['name'];
include(SHARED_PATH . '/public_header.php');
?>
    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($item['garage_id']))); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
            <?php if (can_edit_item($item)) { ?>
                <a class="btn btn-warning action"
                   href="<?php echo url_for('/item/edit.php?id=' . h(u($item['item_id']))); ?>"><i
                            class="bi bi-pencil"></i>Edit Item</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/item/delete.php?id=' . h(u($item['item_id']))); ?>"><i
                            class="bi bi-trash3"></i>Delete Item</a>
            <?php } ?>
        </div>

        <div>
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>Name</th>
                    <td><?php echo h($item['name']); ?></td>
                </tr>
                <?php if (is_owner_or_worker($item)) { ?>
                    <tr>
                        <th>Visibility</th>
                        <td><?php echo $item['visible'] == 1 ? 'Visible to public' : 'Hidden from public'; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>Description</th>
                    <td><?php echo nl2br(stripcslashes($item['description'])); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <h3>Images: <?php echo sizeof($images) . '/' . $max_images; ?></h3>
        <div class="images">
            <?php foreach ($images as $image) {
                list($width, $height) = rescale_image_size($image);

                echo '<a href="' . url_for('image/show.php?id=' . h(u($image['image_id']))) . '">';
                echo '<img src="' . url_for('images/' . $image['source']) . '" width="' . $width . '" height="' . $height . '">';
                echo '</a>';
            } ?>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>