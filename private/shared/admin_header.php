<?php
if (!isset($page_title)) {
    $page_title = 'Admin Area';
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/public.css'); ?>"/>
        <link rel="stylesheet" media="all" href="<?php echo url_for('/stylesheets/admin.css'); ?>"/>
    </head>
<body>

<?php
require('admin_navbar.php');
?>
    <div class="container">
<?php
print_and_delete("message");
print_and_delete("error", "danger");
?>