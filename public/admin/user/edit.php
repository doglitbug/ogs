<?php
global $db;
require_once('../../../private/initialize.php');
require_admin();

$id = get_parameter('id');

$user = $db->get_user($id);
if ($user == null) {
    $_SESSION['error'] = 'User not found';
    redirect_to(url_for('/admin/user'));
}

if (is_post_request()) {
    $user['id'] = $id;
    $user['name'] = clean_input($_POST['name']);
    $user['username'] = clean_input($_POST['username']);
    $user['location_id'] = clean_input($_POST['location_id']);
    $user['locked_out'] = clean_input($_POST['locked_out']);
    $user['description'] = clean_input($_POST['description'], true);

    $errors = validate_user($user);

    if (empty($errors)) {
        $db->update_user($user);
        $_SESSION['message'] = 'User updated successfully';
        redirect_to(url_for('admin/user'));
    }
}

$locations = $db->get_locations();

$page_title = 'Edit User: ' . h($user['username']);
include(SHARED_PATH . '/admin_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/admin/user'); ?>">
                <i class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form class="row g-3" method="post">
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" placeholder="email" aria-label="Email"
                       name="email"
                       value="<?php echo h($user['email']); ?>">
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">Real Name</label>
                <input type="text" class="form-control" placeholder="Real Name" aria-label="Real Name"
                       name="name"
                       value="<?php echo h($user['name']); ?>">
                <?php validation('name'); ?>
            </div>
            <div class="col-md-6">
                <label for="username" class="form-label">Username (shown to others)</label>
                <input type="text" class="form-control" placeholder="Username" aria-label="Username"
                       name="username"
                       value="<?php echo h($user['username']); ?>">
                <?php validation('username'); ?>
            </div>
            <div class="col-md-6">
                <label for="location_id" class="form-label">Location (used for default Garage location)</label>
                <select class="form-select" name="location_id" aria-label="Location">
                    <?php
                    foreach ($locations as $location) {
                        echo "<option value=\"{$location['location_id']}\"";
                        if ($user['location_id'] == $location['location_id']) {
                            echo " selected";
                        }
                        echo ">{$location['description']}</option>";
                    }
                    ?>
                </select>
                <?php validation('location_id'); ?>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Description (about yourself)</label>
                <textarea class="form-control" placeholder="About me" aria-label="Description"
                          name="description"
                          rows="5"><?php echo stripcslashes($user['description']); ?></textarea>
                <?php validation('description'); ?>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="locked_out" value="0"/>
                    <input class="form-check-input" type="checkbox" name="locked_out" value="1"
                           id="locked_out" <?php if ($user['locked_out'] == 1) echo "checked"; ?>>
                    <label class="form-check-label" for="locked_out">
                        Locked out?
                    </label>
                </div>
                <?php validation('locked_out'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-warning">Edit User</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/admin_footer.php'); ?>