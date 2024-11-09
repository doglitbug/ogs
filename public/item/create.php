<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$item = [];
$id = $_GET['garage_id'] ?? '0';

$garage = $db->get_garage($id);
if ($garage == null) {
    $_SESSION['error'] = 'Garage not found';
    redirect_to(url_for('/garage/index.php'));
}

if (!is_owner_or_worker($garage)) {
    $_SESSION['error'] = 'You do not have authority to add items to that Garage';
    redirect_to(url_for('/garage/show.php?id=' . h(u($garage['garage_id']))));
}

if (is_post_request()) {
    $item['garage_id'] = $garage['garage_id'];
    $item['name'] = $_POST['name'] ?? '';
    $item['description'] = $_POST['description'] ?? '';
    $item['visible'] = $_POST['visible'] ?? '';

    $errors = validate_item($item);

    if (empty($errors)) {
        dump($item);
        $new_id = $db->insert_item($item);
        $_SESSION['message'] = 'Item created successfully';
        redirect_to(url_for('/item/show.php?id=' . $new_id));
    }
} else {
    $item['name'] = '';
    $item['description'] = '';
    $item['visible'] = '1';
}

$page_title = 'Add item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>">Back</a>
        </div>

        <form action="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Item name" aria-label="Item name" name="name"
                       value="<?php echo h($item['name']); ?>">
                <?php if (isset($errors['name'])) {
                    echo '<div class="text-danger">' . $errors['name'] . '</div>';
                } ?>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <input type="text" class="form-control" placeholder="Item description" aria-label="Description"
                       name="description"
                       value="<?php echo h($item['description']); ?>">
                <?php if (isset($errors['description'])) {
                    echo '<div class="text-danger">' . $errors['description'] . '</div>';
                } ?>
            </div>

            <div class="mb-3">
                <input type="hidden" name="visible" value="0"/>
                <input class="form-check-input" type="checkbox" name="visible" value="1"
                       id="visible" <?php if ($item['visible'] == 1) echo "checked"; ?>>
                <label class="form-check-label" for="visible">
                    Visible to public?
                </label>
            </div>

            <div id="operations">
                <button type="submit" class="btn btn-warning">Add Item</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>