<?php
global $db;
require_once('../../private/initialize.php');
require_login();

//We can only edit ourselves on the public side
$id = $_SESSION['user_id'];

$user = $db->get_user($id);
if ($user == null) {
    $_SESSION['error'] = 'User not found';
    redirect_to(url_for('/'));
}

if (is_post_request()) {
    $user['name'] = clean_input($_POST['name'], []) ?? '';
    $user['username'] = clean_input($_POST['username'], []) ?? '';
    $user['location_id'] = $_POST['location_id'] ?? '';
    $user['description'] = clean_input($_POST['description']) ?? '';

    $errors = validate_user($user);

    dump($errors);

    if (empty($errors)) {
        $db->update_user($user);
        $_SESSION['message'] = 'User updated successfully';
        redirect_to(url_for('/user/show.php'));
    }
}

$locations = $db->get_all_locations();

$page_title = 'Edit User: ' . h($user['username']);
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/user/show.php'); ?>">
                <i class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form class="row g-3" action="<?php echo url_for('/user/edit.php'); ?>" method="post">
            <div class="col-md-6">
                <label for="email" class="form-label">Email (cannot be changed)</label>
                <input type="text" class="form-control" placeholder="email" aria-label="Email"
                       name="email"
                       disabled
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
                          rows="5"><?php echo $user['description']; ?></textarea>
                <?php validation('description'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-warning">Edit User</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>