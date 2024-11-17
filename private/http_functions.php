<?php


/** Generate URL from WWW_ROOT + $script_path
 * @param string $script_path input
 * @return string output
 */
function url_for(string $script_path): string
{
    // Add leading '/' if not present
    if ($script_path[0] != '/') {
        $script_path = "/" . $script_path;
    }
    return WWW_ROOT . $script_path;
}

/** Apply urlencode to provided value
 * @param string|null $string input
 * @return string output
 */
function u(?string $string = ""): string
{
    return urlencode($string);
}

/** Apply rawurlencode to provided value
 * @param string|null $string input
 * @return string output
 */
function raw_u(?string $string = ""): string
{
    return rawurlencode($string);
}

/** Apply htmlspecialchars to provided value
 * @param string|null $string input
 * @return string
 */
function h(?string $string = ""): string
{
    return htmlspecialchars($string);
}

/** Return a 404 error
 */
function error_404(): void
{
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Page Not Found");
    exit();
}

/** Return a 500 error
 */
function error_500(): void
{
    header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    exit();
}

/** Return a generic database error
 */
function error_database(string $error_message = "Unspecified Database Error"): void
{
    header($_SERVER["SERVER_PROTOCOL"] . " 500 " . $error_message);
    exit();
}

/** Return a 302 redirect to the provided location
 * @param string $location url to redirect to
 */
function redirect_to(string $location): void
{
    if (isset($db)) $db->disconnect();
    header('Location: ' . $location);
    exit;
}

/** Is this request the result of a form submission?
 * @return bool POST Request
 */
function is_post_request(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/** Is this request the result of a normal submission?
 * @return bool GET Request
 */
function is_get_request(): bool
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

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

/** Provides new width and height for an image, scaled to the provided size
 * @param array $image
 * @param int $max_size maximum size for width/height
 * @return array new width, new height
 */
function rescale_image(array $image, int $max_size = 256): array
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