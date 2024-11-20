<?php
global $db;
require_once('../../private/initialize.php');

$public_garages = $db->get_all_garages(['visible' => '1']);
$my_garages = [];
if (is_logged_in()) {
    $my_garages = $db->get_garages_by_user($_SESSION['user_id']);
}

$page_title = 'Garages';
include(SHARED_PATH . '/public_header.php');
?>

    <div id="content">
        <?php if (is_logged_in()) { ?>
            <h1><?php echo $page_title; ?></h1>
            <div class="cta">
                <a class="btn btn-success action" href="<?php echo url_for('/garage/create.php'); ?>"><i class="bi bi-plus-lg"></i>New
                    Garage</a>
            </div>
        <?php } ?>
        <?php if (is_logged_in()) { ?>
            <h1>My garages</h1>
            <div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Visible to public?</th>
                    </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
            </div>

            <h1>Shared garages</h1>
            <div>
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Visible to public?</th>
                    </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
            </div>

        <?php } ?>
        <div>
            <h1>Public garages</h1>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Location</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($public_garages as $garage) { ?>
                    <tr>
                        <td><a class="action"
                               href="<?php echo url_for('/garage/show.php?id=' . h(u($garage['garage_id']))); ?>">
                                <?php echo h($garage['name']); ?></a></td>
                        <td><?php echo h($garage['description']); ?></td>
                        <td><?php echo h($garage['location']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include(SHARED_PATH . '/public_footer.php'); ?>