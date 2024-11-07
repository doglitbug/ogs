<?php
global $db;
require_once('../../private/initialize.php');

$garages = $db->get_all_garages(['visible' => '1']);
$my_garages = [];
if (is_logged_in()) {
    $my_garages = $db->get_garages_by_user($_SESSION['user_id']);
}

$page_title = 'Garages';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <h1><?php echo $page_title; ?></h1>

        <div class="cta">
            <?php if (is_logged_in()) { ?>
                <a class="btn btn-primary action" href="<?php echo url_for('/garage/create.php'); ?>">Create new
                    Garage</a>
            <?php } ?>
        </div>
        <?php if (is_logged_in()) { ?>
            <h1>My owned garages</h1>
            <div>
                <table class="table">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Visible</th>
                    </tr>
                    <?php foreach (array_filter($my_garages, function ($g) {
                        return $g['access'] == "Owner";
                    }) as $garage) { ?>
                        <tr>
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

            <h1>Garages where I'm a worker</h1>
            <div>
                <table class="table">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Visible</th>
                    </tr>
                    <?php foreach (array_filter($my_garages, function ($g) {
                        return $g['access'] == "Worker";
                    }) as $garage) { ?>
                        <tr>
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

        <?php } ?>
        <div>
            <h1>Public Garages</h1>
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Visible</th>
                </tr>
                <?php foreach ($garages as $garage) { ?>
                    <tr>
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