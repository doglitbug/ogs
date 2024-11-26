<?php
global $db;
require_once('../../private/initialize.php');
require_login();

//If no id parameter, show current user
$id = get_parameter('id');
if ($id === "") $id = $_SESSION['user_id'];

$user = $db->get_user($id);
if ($user == null) {
    $_SESSION['error'] = 'User not found';
    redirect_to(url_for('/'));
}

$page_title = 'User Details: ' . h($user['username']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/'); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
            <?php if ($user['user_id'] == $_SESSION['user_id']) { ?>
                <a class="btn btn-warning action"
                   href="<?php echo url_for('/user/edit.php'); ?>"><i
                            class="bi bi-pencil"></i>Edit User</a>
                <a class="btn btn-danger action"
                   href="<?php echo url_for('/user/delete.php'); ?>"><i
                            class="bi bi-trash3"></i>Delete User</a>
            <?php } ?>
        </div>

        <div>
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th>Email</th>
                    <td><?php echo h($user['email']); ?></td>
                </tr>
                <?php if ($user['user_id'] == $_SESSION['user_id']) { ?>
                    <tr>
                        <th>Real Name</th>
                        <td><?php echo h($user['name']); ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <th>Username</th>
                    <td><?php echo h($user['username']); ?></td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td><?php echo h($user['location']); ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?php echo h($user['description']); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>