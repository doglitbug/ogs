<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$options['search'] = get_parameter("search");
$max_items = sizeof($db->get_items($options));

$options['paginate'] = 'true';
$items = $db->get_items($options);
$page_title = 'Items';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php generate_search($options['search']); ?>
    <div>
        <table class="table">
            <tr>
                <th>item_id</th>
                <th>Preview</th>
                <th>Name</th>
                <th>Description</th>
                <th>Visible</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td><?php echo h($item['item_id']); ?></a></td>
                    <td><?php
                        if ($item['image_id'] != 0) {
                            list($width, $height) = rescale_image_size($item, 96);

                            echo '<a href="' . url_for('image/show.php?id=' . h(u($item['image_id']))) . '">';
                            echo '<img src="' . url_for('images/' . h($item['source'])) . '" width="' . $width . '" height="' . $height . '">';
                            echo '</a>';
                        } else {
                            echo "&nbsp;";
                        }
                        ?></td>
                    <td><?php echo h($item['name']); ?></a></td>
                    <td><?php echo nl2br(stripcslashes($item['description'])); ?></a></td>
                    <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                    <td><?php echo h($item['created_at']); ?></a></td>
                    <td><?php echo h($item['updated_at']); ?></a></td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/item/show.php?id=' . h(u($item['item_id']))); ?>">View</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/item/edit.php?id=' . h(u($item['item_id']))); ?>">Edit</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/item/delete.php?id=' . h(u($item['item_id']))); ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php generate_pagination_links($max_items); ?>
    </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
