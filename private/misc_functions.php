<?php
/** Print out a session stored message and unset it
 * @param string $name message
 * @param string $type Bootstrap alert type
 * @return void
 */
function print_and_delete(string $name, string $type = "primary"): void
{
    if (!isset($_SESSION[$name]) || $_SESSION[$name] == "") {
        return;
    }

    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
    echo h($_SESSION[$name]);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION[$name]);
}

/** Output validation errors onto forms if available
 * @param string $name
 * @return void
 */
function validation(string $name): void
{
    global $errors;
    if (isset($errors[$name])) {
        echo '<div class="text-danger">' . $errors[$name] . '</div>';
    }
}

/** Debug.print a variable
 * @param mixed $variable Variable to print out
 * @return void
 */
function dump(mixed $variable): void
{
    echo '<pre>';
    print_r($variable);
    echo '</pre>';
}

/** Provides new width and height for an image, scaled to the provided max size
 * @param array $image
 * @param int $max_size maximum size for width/height
 * @return array new width, new height
 */
function rescale_image_size(array $image, int $max_size = 256): array
{
    $width = $image['width'];
    $height = $image['height'];
    $max = max($width, $height);
    $scale = $max_size / $max;

    return [floor($width * $scale), floor($height * $scale)];
}

/** Move and link uploaded images
 * @param array $images Images from $_FILES
 * @param int $item_id Item to link the new images to
 * @return void
 */
function move_and_link_images(array $images, int $item_id): void
{
    global $db;
    //Move and link images
    foreach ($images as $image) {
        //Check that this file is valid
        if ($image['error'] != 0) continue;
        //Get dimensions of image for database
        list($width, $height) = getimagesize($image['tmp_name']);
        $image['width'] = $width;
        $image['height'] = $height;
        $image['path'] = "item";

        //Clean name and make unique
        $path_info = pathinfo($image['name']);
        $base = $path_info['filename'];
        $base = preg_replace("/[^\w-]/", "_", $base);
        $image['filename'] = time() . $base . "." . $path_info['extension'];

        //TODO Check for success!
        move_uploaded_file($image['tmp_name'], PUBLIC_PATH . '/images/' . $image['path'] . '/' . $image['filename']);
        $image_id = $db->insert_image($image);
        //Create item_image link
        $db->insert_item_image($item_id, $image_id);
    }
}

/** Generate page based search form
 * @param string $search previous search term
 * @param string $destination form action URL
 * @return void
 */
function generate_search(string $search = "", string $destination = ""): void
{
    echo '<form action = "' . $destination . '" role="search" method="get">';
    echo '<div class="input-group mb-2">';
    echo '<input class="form-control" name="search" type="search" placeholder="Search" aria-label="Search" value="' . h($search) . '">';
    echo '<button class="btn btn-primary" type="submit">Search</button>';
    echo '</div>';
    echo '</form>';
}

/** Generate pagination links
 * @param string $total_size
 * @return void
 */
function generate_pagination_links(string $total_size): void
{
    list ($current_page, $size) = get_page_and_size();

    $base_url = $_SERVER['PHP_SELF'];
    $stripped_query = '?';
    //Strip out the page params, but keep all others, eg search and size!
    if (!empty($_SERVER['QUERY_STRING'])) {
        $parsed = parse_url($base_url . '?' . $_SERVER['QUERY_STRING']);
        $query = $parsed['query'];
        parse_str($query, $params);
        unset($params['page']);

        if ($params) {
            $stripped_query .= http_build_query($params) . "&";
        }
    }

    $base_url .= $stripped_query;
    //Adjust for no results found
    $last_page = max(floor(((int)$total_size - 1) / $size) + 1, 1);
    //Keep 5 on screen at all times when possible
    $start_page = max(1, min($current_page - 2, $last_page - 4));
    $end_page = min($start_page + 4, $last_page);

    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-center">';
    $disabled = ($current_page == 1) ? " disabled" : "";
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=1"><i class="bi bi-chevron-double-left"></i></a></li>';
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . ($current_page - 1) . '"><i class="bi bi-chevron-left"></i></a></li>';

    for ($i = $start_page; $i <= $end_page; $i++) {
        $active = $i == $current_page ? " active" : "";
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
    }

    $disabled = ($current_page == $last_page) ? " disabled" : "";
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . ($current_page + 1) . '"><i class="bi bi-chevron-right"></i></a></li>';
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . $last_page . '"><i class="bi bi-chevron-double-right"></i></a></li>';
    echo '</ul>';
    echo '<div class="centered">';
    if ($total_size != 0) {
        echo ($current_page - 1) * $size + 1 . '-' . min($current_page * $size, $total_size) . ' of ' . $total_size . ' results';
    } else {
        echo 'No results found';
    }
    echo '</nav>';
}

/** Get the page and size from $_GET parameters
 * @return int[] Page number, Size
 */
function get_page_and_size(): array
{
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] < 1000 ? (int)$_GET['page'] : 1;
    $size = isset($_GET['size']) && is_numeric($_GET['size']) && $_GET['size'] > 0 && $_GET['size'] < 1000 ? (int)$_GET['size'] : 10;
    return [$page, $size];
}

?>


