<?php
if (!isset($page_title)) {
    $page_title = 'Online Garage Sale';
}
?>

    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>OGS - <?php echo h($page_title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
              crossorigin="anonymous">
        <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/public.css'); ?>"/>
    </head>
<body>
<header>
    <h1>Online Garage Sale</h1>
</header>

<nav>
    <ul>
        <?php
        if (is_logged_in()) {
            ?>
            <li>User: <?php echo $_SESSION['username'] ?? ''; ?></li>
            <li><a href="<?php echo url_for('/auth/logout.php'); ?>">Logout</a></li>
            <?php
        }
        ?>
    </ul>
</nav>

<?php
print_and_delete("message");
print_and_delete("error");
?>