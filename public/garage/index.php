<?php
global $db;
require_once('../../private/initialize.php');
require_login();

$garages = $db->get_garages_by_user($_SESSION['user_id']);

$page_title = 'Garages';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <a class="btn btn-primary action" href="<?php echo url_for('/garage/create.php'); ?>">Create new Garage</a>
        </div>

        <p>These are the garages you have access to: as an owner or worker.<br>
            Free accounts are limited to 1 garage each with up to 2 workers.
        </p>

        <div>
            <table class="table">
                <tr>
                    <th>Access</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Visible</th>
                </tr>
                <?php foreach ($garages as $garage) { ?>
                    <tr>
                        <td><?php echo h($garage['access']); ?></td>
                        <td><a class="action"
                               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>">
                                <?php echo h($garage['name']); ?></a></td>
                        <td><?php echo h($garage['description']); ?></td>
                        <td><?php echo h($garage['location']); ?></td>
                        <td><?php echo $garage['visible'] == 1 ? 'Visible' : 'Hidden'; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>