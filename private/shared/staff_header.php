<?php
if (!isset($page_title)) {
    $page_title = 'Staff Area';
}
?>

    <!doctype html>

    <html lang="en">
    <head>
        <title>OGS - <?php echo h($page_title); ?></title>
        <meta charset="utf-8">
        <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/staff.css'); ?>"/>
    </head>
<body>
<header>
    <h1>OGS Staff Area</h1>
</header>

<nav>
    <ul>
        <li>User: <?php echo $_SESSION['username'] ?? ''; ?></li>
        <li><a href="<?php echo url_for('/staff/index.php'); ?>">Menu</a></li>
        <li><a href="<?php echo url_for('/auth/logout.php'); ?>">Logout</a></li>
    </ul>
</nav>

<?php
print_and_delete("message");
print_and_delete("error");
?>