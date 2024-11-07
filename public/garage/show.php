<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$id = $_GET['id'] ?? '1';

$garage = $db->get_garage($id);
$items = $db->get_all_items(['garage_id' => $garage['garage_id']]);

$page_title = 'Garage';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/index.php'); ?>">Back</a>
            <?php if (is_owner($_SESSION['user_id'], $garage['garage_id'])) {
                ?>
                <a class="btn btn-primary action"
                   href="<?php echo url_for('/garage/edit.php?id=' . h(u($garage['garage_id']))); ?>">Edit Garage</a>
                <a class="btn btn-primary action"
                   href="<?php echo url_for('/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>">Delete
                    Garage</a>
            <?php } ?>
        </div>

        <div>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Visible</th>
                </tr>
                <tr>
                    <td><?php echo h($garage['name']); ?></td>
                    <td><?php echo h($garage['description']); ?></td>
                    <td><?php echo h($garage['location']); ?></td>
                    <td><?php echo $garage['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                </tr>
            </table>
        </div>

        <h1>Items</h1>
        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>">Add Items</a>
        </div>
        <div>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Visible</th>
                </tr>
                <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?php echo h($item['name']); ?></td>
                        <td><?php echo h($item['description']); ?></td>
                        <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>