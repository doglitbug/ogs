<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$page_title = 'Profile';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>
        <p>This will be the profile page!</p>
        <pre>
        <?php var_dump($_SESSION); ?>
    </pre>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>