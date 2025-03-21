<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$options['search'] = get_parameter("search");
$max_garages = sizeof($db->get_garages($options));

$options['paginate'] = 'true';
$garages = $db->get_garages($options);

$page_title = 'Garages';
include(SHARED_PATH . '/admin_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php generate_search($options['search']); ?>
    <div>
        <table class="table">
            <tr>
                <th>garage_id</th>
                <th>Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Visible</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($garages as $garage) { ?>
                <tr>
                    <td><?php echo h($garage['garage_id']); ?></a></td>
                    <td><?php echo h($garage['name']); ?></a></td>
                    <td><?php echo nl2br(stripcslashes($garage['description'])); ?></td>
                    <td><?php echo h($garage['location']); ?></td>
                    <td><?php echo $garage['visible'] == 1 ? 'Yes' : 'No'; ?></a></td>
                    <td><?php echo h($garage['created_at']); ?></a></td>
                    <td><?php echo h($garage['updated_at']); ?></a></td>

                    <td><a class="action"
                           href="<?php echo url_for('/admin/garage/edit.php?id=' . h(u($garage['garage_id']))); ?>">View/Edit</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/admin/garage/delete.php?id=' . h(u($garage['garage_id']))); ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php generate_pagination_links($max_garages); ?>
    </div>
</div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>
