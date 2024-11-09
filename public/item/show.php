<?php
global $db;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '1';

$item = $db->get_item($id);
if ($item == null || ($item['visible'] == '0' && !can_edit_item($item))) {
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

$page_title = 'Show Item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action" href="javascript:history.back()">Back</a>
            <?php if (can_edit_item($item)) {
                ?>
                <a class="btn btn-warning action"
                   href="<?php echo url_for('/item/edit.php?id=' . h(u($item['item_id']))); ?>">Edit</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/item/delete.php?id=' . h(u($item['item_id']))); ?>">Delete</a>
            <?php } ?>
        </div>

        <div>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Visible to public?</th>
                </tr>
                <tr>
                    <td><?php echo h($item['name']); ?></td>
                    <td><?php echo h($item['description']); ?></td>
                    <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                </tr>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>