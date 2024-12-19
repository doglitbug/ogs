<?php
global $db;
require_once('../../private/initialize.php');

$search = (get_parameter("search"));
$options['search'] = $search;
$options['visible'] = '1';

$item_count = sizeof($db->get_items($options));
$options['paginate'] = 'true';
$shown_items = $db->get_items($options);

$page_title = 'Search: ' . h($search);
include(SHARED_PATH . '/public_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php generate_search($options['search']); ?>

    <div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Preview</th>
                <th>Name</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($shown_items as $item) {
                ?>
                <tr>
                    <td><?php
                        if ($item['image_id'] != 0) {
                            list($width, $height) = rescale_image_size($item, 96);

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
                    <td><?php echo nl2br(stripcslashes($item['description'])); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php generate_pagination_links($item_count); ?>
    </div>
</div>
<?php include(SHARED_PATH . '/public_footer.php'); ?>
