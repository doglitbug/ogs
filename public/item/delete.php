<?php
global $db;
require_once('../../private/initialize.php');
require_login();

//TODO This whole page
$id = $_GET['id'] ?? '0';

$item = $db->get_item($id);

if ($item == null) {
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

if (!can_edit_item($item)) {
    $_SESSION['error'] = 'You do not have authority to delete that item';
    redirect_to(url_for('/item/show.php?id=' . h(u($item['item_id']))));
}

$images = $db->get_item_images($item['item_id']);

if (is_post_request()) {
    //Must delete item_image links first
    foreach ($images as $image) {
        unlink(PUBLIC_PATH . '/images/' . $image['source']);
        $db->delete_image($image);
    }

    $db->delete_item($item);
    $_SESSION['message'] = 'Item deleted successfully';
    redirect_to(url_for('/garage/show.php?id=' . h(u($item['garage_id']))));
}


$page_title = 'Delete Item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/item/show.php?id=' . h(u($item['item_id']))); ?>"><i class="bi bi-arrow-left"></i>Back</a>
        </div>

        <p>Are you sure you wish to delete this item?</p>
        <p class="item"><?php echo h($item['name']); ?></p>
        <form action="<?php echo url_for('/item/delete.php?id=' . h(u($item['item_id']))); ?>" method="post">
            <div id="operations">
                <button type="submit" class="btn btn-danger">Delete Item</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>