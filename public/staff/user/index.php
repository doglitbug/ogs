<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$options['search'] = get_parameter("search");
$max_users = sizeof($db->get_users($options));

$options['paginate'] = 'true';
$users = $db->get_users($options);

$page_title = 'Users';
include(SHARED_PATH . '/staff_header.php');
?>

<div id="content">
    <h1><?php echo $page_title; ?></h1>
    <?php generate_search($options['search']); ?>
    <div>
        <table class="table">
            <tr>
                <th>user_id</th>
                <th>username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Location</th>
                <th>Locked out</th>
                <th>User type</th>
                <th>created_at</th>
                <th>updated_at</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($users as $user) { ?>
                <tr>
                    <td><?php echo h($user['user_id']); ?></a></td>
                    <td><?php echo h($user['username']); ?></a></td>
                    <td><?php echo h($user['name']); ?></a></td>
                    <td><?php echo h($user['email']); ?></a></td>
                    <td><?php echo h($user['location']); ?></a></td>
                    <td><?php echo $user['locked_out'] == 1 ? 'Yes' : 'No'; ?></a></td>
                    <td><?php echo h($user['access']); ?></a></td>
                    <td><?php echo h($user['created_at']); ?></a></td>
                    <td><?php echo h($user['updated_at']); ?></a></td>


                    <td><a class="action"
                           href="<?php echo url_for('/staff/user/edit.php?id=' . h(u($user['user_id']))); ?>">View/Edit</a>
                    </td>
                    <td><a class="action"
                           href="<?php echo url_for('/staff/user/delete.php?id=' . h(u($user['user_id']))); ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php generate_pagination_links($max_users); ?>
    </div>
</div>

<?php include(SHARED_PATH . '/staff_footer.php'); ?>
