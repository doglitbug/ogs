<?php
global $db;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null || ($garage['visible'] == '0' && !is_owner_or_worker($garage))) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

$options['garage_id'] = $garage['garage_id'];

//Cache some database calls!
$is_owner = is_owner($garage['garage_id']);
$is_worker = is_worker($garage['garage_id']);
$is_owner_or_worker = $is_owner || $is_worker;

//Hide hidden items unless owner/worker
if (!($is_owner_or_worker)) $options['visible'] = '1';

$search = get_parameter("search");
$options['search'] = $search;

$max_items = sizeof($db->get_items($options));
$options['paginate'] = 'true';
$shown_items = $db->get_items($options);

$garage_staff = $db->get_garage_staff($garage['garage_id']);

$page_title = 'Show Garage: ' . h($garage['name']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/index.php'); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
            <?php if ($is_owner) { ?>
                <a class="btn btn-warning action"
                   href="<?php echo url_for('/garage/edit.php?id=' . h(u($garage['garage_id']))); ?>"><i
                            class="bi bi-pencil"></i>Edit
                    Garage</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>"><i
                            class="bi bi-trash3"></i>Delete
                    Garage</a>
            <?php } ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h2>Details:</h2>
                <table class="table table-hover">
                    <tbody>
                    <tr>
                        <th>Name</th>
                        <td><?php echo h($garage['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td><?php echo h($garage['location']); ?></td>
                    </tr>
                    <?php if ($is_owner_or_worker) { ?>
                        <tr>
                            <th>Visible</th>
                            <td><?php echo $garage['visible'] == 1 ? 'Visible to public' : 'Hidden from public'; ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th>Description</th>
                        <td><?php echo nl2br(stripcslashes($garage['description'])); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h2>Contact:</h2>
                <table class="table table-hover">
                    <?php if (is_logged_in()) {
                        foreach ($garage_staff as $gsm) {
                            echo '<tr><th>' . $gsm['access'] . '</th>';
                            echo '<td><a href="' . url_for("/user/show.php?id=" . h(u($gsm['user_id']))) . '">' . h(u($gsm['username'])) . '</a>';
                            echo '</td></tr>';
                        }
                    } else {
                        echo '<tr><td>This is only available to registered users.<br/>Please click <a href="' . url_for("auth/login.php") . '">here</a> to log in or sign up</td></tr>';
                    } ?>
                </table>
            </div>
        </div>


        <h1>Items:</h1>
        <div class="cta">
            <?php if ($is_owner_or_worker) {
                ?>

                <a class="btn btn-success action"
                   href="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>"><i
                            class="bi bi-plus-lg"></i>Add
                    Item</a>

            <?php } ?>

            <?php generate_search($options['search'], "", ['id' => h(u($garage['garage_id']))]); ?>
        </div>

        <div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Preview</th>
                    <th>Name</th>
                    <th>Description</th>
                    <?php if ($is_owner_or_worker) { ?>
                        <th>Visible to public?</th>
                    <?php } ?>
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
                        <?php if ($is_owner_or_worker) { ?>
                            <td><?php echo $item['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php generate_pagination_links($max_items); ?>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>