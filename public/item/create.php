<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$item = [];
$errors = [];
$id = $_GET['garage_id'] ?? '0';

//TODO Allow null garage and have a drop down to change garage!
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
    $item['name'] = clean_input($_POST['name'], []) ?? '';
    $item['description'] = clean_input($_POST['description']) ?? '';
    $item['visible'] = $_POST['visible'] ?? '';

    $errors = validate_item($item, $_FILES);

    if (empty($errors)) {
        $item_id = $db->insert_item($item);
        move_and_link_images($_FILES, $item_id);

        $_SESSION['message'] = 'Item created successfully';
        redirect_to(url_for('/item/show.php?id=' . h(u($item_id))));
    }
} else {
    $item['name'] = '';
    $item['description'] = '';
    $item['visible'] = '1';
}

$page_title = 'Add Item';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action"
               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>"><i
                        class="bi bi-arrow-left"></i>Back</a>
        </div>

        <form class="row g-3"
              action="<?php echo url_for('/item/create.php?garage_id=' . h(u($garage['garage_id']))); ?>" method="post"
              enctype="multipart/form-data">
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" placeholder="Item name" aria-label="Item name" name="name"
                       value="<?php echo h($item['name']); ?>">
                <?php validation('name'); ?>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="hidden" name="visible" value="0"/>
                    <input class="form-check-input" type="checkbox" name="visible" value="1"
                           id="visible" <?php if ($item['visible'] == 1) echo "checked"; ?>>
                    <label class="form-check-label" for="visible">
                        Visible to public?
                    </label>
                    <?php validation('visible'); ?>
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" placeholder="Item description" aria-label="Description"
                          name="description"
                          rows="5"><?php echo stripcslashes($item['description']); ?></textarea>
                <?php validation('description'); ?>
            </div>


            <h3>Add Image:</h3>
            <div class="col-12">
                <input type="file" id="images" name="images">
                <?php validation('images'); ?>
            </div>

            <div class="col-12" id="operations">
                <button type="submit" class="btn btn-warning">Add Item</button>
            </div>
        </form>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>