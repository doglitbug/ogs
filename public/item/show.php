<?php
global $db;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '1';

$item = $db->get_item($id);
if ($item == null || ($item['visible'] == '0' && !can_edit_item($item))) {
    //TODO Check if garage is hidden
    $_SESSION['error'] = 'Item not found';
    redirect_to(url_for('/item/index.php'));
}

$page_title = 'Show Item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($item['garage_id']))); ?>">Back</a>
            <?php if (can_edit_item($item)) { ?>

                <a class="btn btn-success action"
                   href="<?php echo url_for('/item/create.php?garage_id=' . h(u($item['garage_id']))); ?>">Add</a>

                <a class="btn btn-warning action"
                   href="<?php echo url_for('/item/edit.php?id=' . h(u($item['item_id']))); ?>">Edit</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/item/delete.php?id=' . h(u($item['item_id']))); ?>">Delete</a>
            <?php } ?>
        </div>

        <div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <?php if (can_edit_item($item)) { ?>
                        <th>Visible to public?</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo h($item['name']); ?></td>
                    <td><?php echo h($item['description']); ?></td>
                    <?php if (can_edit_item($item)) { ?>
                        <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>