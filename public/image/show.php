<?php
global $db;
require_once('../../private/initialize.php');

$id = $_GET['id'] ?? '0';

$image = $db->get_image($id);
if (!$image){
    //TODO Better error handling here, maybe a cta with back?
    die("Image not found");
}

//TODO Check if the item(or its garage is visible)?

$page_title = 'Show Image';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <?php
        echo '<img src="' . url_for('images/' . $image['source']) . '" width="' . $image['width'] . '" height="' . $image['height'] . '">';
        ?>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>