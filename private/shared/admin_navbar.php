<?php
$sections = array(
    "Settings" => url_for("admin/setting"),
    "Users" => url_for("admin/user"),
    "Garages" => url_for("admin/garage"),
    "Items" => url_for("admin/item"),
    "Customer" => url_for("/"),
    "Log out" => url_for('auth/logout.php')
);

//Get from current URL
//TODO fix this
$current = basename(dirname($_SERVER['PHP_SELF']));
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo url_for('admin'); ?>">Online Garage Sale</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                foreach ($sections as $name => $location) {
                    $active = strcasecmp($current, $name) == 0;
                    ?>
                    <li class="nav-item">
                        <?php
                        if ($active) {
                            echo '<a class="nav-link active" aria-current="page" href="' . $location . '">' . $name . '</a>';
                        } else {
                            echo '<a class="nav-link inactive" href="' . $location . '">' . $name . '</a>';
                        }
                        ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
</nav>