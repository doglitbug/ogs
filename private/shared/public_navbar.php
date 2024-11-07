<?php
$sections['Garages'] = url_for("garage");
$sections['Items'] = url_for("item");

if (is_logged_in()) {
    if (is_admin($_SESSION['user_id']) || is_super_admin($_SESSION['user_id'])) {
        $sections['Staff account'] = url_for("staff");
    }
    $sections['Profile'] = url_for('auth/profile.php');
    $sections['Log out'] = url_for('auth/logout.php');
} else {
    $sections['Log in'] = url_for("auth/login.php");
    $sections['Log in as'] = url_for("auth/deleteme.php");
}

//Get from current URL
//TODO fix this
$current = basename(dirname($_SERVER['PHP_SELF']));
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo url_for('/'); ?>">Online Garage Sale</a>
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

            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>