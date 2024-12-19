<?php
$sections['Garages'] = url_for("garage");
$sections['Items'] = url_for("item");

if (is_logged_in()) {
    if (is_admin($_SESSION['user_id']) || is_super_admin($_SESSION['user_id'])) {
        $sections['Admin'] = url_for("admin");
    }
    $sections['Profile'] = url_for('user/show.php');
    $sections['Log out'] = url_for('auth/logout.php');
} else {
    $sections['Log in'] = url_for("auth/login.php");
}

//Get from current URL
//TODO fix this
$current = basename(dirname($_SERVER['PHP_SELF']));
?>

<nav class="navbar navbar-expand-md">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo url_for('/'); ?>">
            <span class="d-lg-inline-block d-none">Online Garage Sale</span>
            <span class="d-inline-block d-lg-none">OGS</span>
         </a>
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