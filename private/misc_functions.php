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

/** Generate pagination links
 * @param string $total_size
 * @return void
 */
function generate_pagination_links(string $total_size): void
{
    list ($page, $size) = get_page_and_size();

    $base_url = $_SERVER['PHP_SELF'];
    $stripped_query = '?';
    //Strip out the page and size params, but keep all others, eg search
    if (!empty($_SERVER['QUERY_STRING'])) {
        $parsed = parse_url($base_url . '?' . $_SERVER['QUERY_STRING']);
        $query = $parsed['query'];
        parse_str($query, $params);
        unset($params['size']);
        unset($params['page']);

        if ($params) {
            $stripped_query .= http_build_query($params) . "&";
        }
    }

    $base_url .= $stripped_query;
    $last_page = floor(((int)$total_size - 1) / $size) + 1;
    //Keep 5 on screen at all times.
    //TODO Find a better way to do this?
    $start_page = max(1, $page - 2);

    $max_page = min($last_page, $start_page + 4);
    $start_page = min($start_page, $max_page - 4);

    echo '<nav aria-label="Page navigation">';
    echo '<ul class="pagination justify-content-center">';
    $disabled = ($page == 1) ? " disabled" : "";
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=1"><i class="bi bi-chevron-double-left"></i></a></li>';
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . ($page - 1) . '"><i class="bi bi-chevron-left"></i></a></li>';

    for ($i = $start_page; $i <= $max_page; $i++) {
        $active = $i == $page ? " active" : "";
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $base_url . 'page=' . $i . '">' . $i . '</a></li>';
    }

    $disabled = ($page == $last_page) ? " disabled" : "";
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . ($page + 1) . '"><i class="bi bi-chevron-right"></i></a></li>';
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $base_url . 'page=' . $last_page . '"><i class="bi bi-chevron-double-right"></i></a></li>';
    echo '</ul>';
    echo '<div class="centered">' . ($page - 1) * $size + 1 . '-' . min($page * $size, $total_size) . ' of ' . $total_size . ' results</div>';
    echo '</nav>';
}

/** Get the page and size from $_GET parameters
 * @return int[] Page number, Size
 */
function get_page_and_size(): array
{
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $size = isset($_GET['size']) && is_numeric($_GET['size']) && $_GET['size'] > 0 ? (int)$_GET['size'] : 5;
    return [$page, $size];
}

?>


