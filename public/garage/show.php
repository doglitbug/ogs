<?php
global $db;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null || ($garage['visible'] == '0' && !is_owner_or_worker($garage))) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}
$items = $db->get_all_items(['garage_id' => $garage['garage_id']]);

$page_title = 'Show Garage: ' . h($garage['name']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/index.php'); ?>"><i class="bi bi-arrow-left"></i>Back</a>
            <?php if (is_logged_in()) { ?>
                <a class="btn btn-success action" href="<?php echo url_for('/garage/create.php'); ?>"><i class="bi bi-plus-lg"></i>New
                    Garage</a>
            <?php } ?>
            <?php if (is_owner($garage['garage_id'])) { ?>
                <a class="btn btn-warning action"
                   href="<?php echo url_for('/garage/edit.php?id=' . h(u($garage['garage_id']))); ?>"><i class="bi bi-pencil"></i>Edit
                    Garage</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>"><i class="bi bi-trash3"></i>Delete
                    Garage</a>
            <?php } ?>
        </div>

        <div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <?php if (is_owner_or_worker($garage)) { ?>
                        <th>Visible to public?</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?php echo h($garage['name']); ?></td>
                    <td><?php echo h($garage['description']); ?></td>
                    <td><?php echo h($garage['location']); ?></td>
                    <?php if (is_owner_or_worker($garage)) { ?>
                        <td><?php echo $garage['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                    <?php } ?>
                </tr>
                </tbody>
            </table>
        </div>

        <h1>Items</h1>
        <?php if (is_owner_or_worker($garage)) {
            ?>
            <div class="cta">
                <a class="btn btn-success action"
                   href="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>"><i class="bi bi-plus-lg"></i>Add
                    Item</a>
            </div>
        <?php } ?>

        <div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Preview</th>
                    <th>Name</th>
                    <th>Description</th>
                    <?php if (is_owner_or_worker($garage)) { ?>
                        <th>Visible to public?</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $item) {
                    if ($item['visible'] == '0' && !can_edit_item($item)) continue;
                    ?>
                    <tr>
                        <td><?php
                            if ($item['image_id'] != 0) {
                                list($width, $height) = rescale_image($item, 96);

                                echo '<a href="' . url_for('image/show.php?id=' . h(u($item['image_id']))) . '">';
                                echo '<img src="' . url_for('images/' . h($item['source'])) . '" width="' . $width . '" height="' . $height . '">';
                                echo '</a>';
                            } else {
                                echo "&nbsp;";
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo url_for('/item/show.php?id=' . h(u($item['item_id']))); ?>"><?php echo h($item['name']); ?></a>
                        </td>
                        <td><?php echo h($item['description']); ?></td>
                        <?php if (is_owner_or_worker($garage)) { ?>
                            <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>