<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$items = $db->get_all_items();
$page_title = 'Items';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <div>
        <table class="table">
            <tr>
                <th>item_id</th>
                <th>Name</th>
                <th>Description</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo h($item['item_id']); ?></a></td>
                    <td><?php echo h($item['name']); ?></a></td>
                    <td><?php echo h($item['description']); ?></a></td>
                    <td><?php echo h($item['created_at']); ?></a></td>
                    <td><?php echo h($item['updated_at']); ?></a></td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/user/show.php?id=' . h(u($item['item_id']))); ?>">View</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/user/edit.php?id=' . h(u($item['item_id']))); ?>">Edit</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/user/delete.php?id=' . h(u($item['item_id']))); ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
