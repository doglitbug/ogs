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

/** Apply urlencode to provided value. Used when text is to be part of a URL
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

/** Apply htmlspecialchars to provided value. Used to convert special characters to html equiv
 * @param string|null $string input
 * @return string
 */
function h(?string $string = ""): string
{
    return htmlspecialchars($string);
}

/** Attempt to clean up user input
 * @param string|null $input
 * @param bool $allow_html If true, allow a subset of html tags such as b and i
 * @return string
 */
function clean_input(string|null $input = "", bool $allow_html = false): string
{
    global $db;
    $allowed_tags = [];
    if ($allow_html) {
        $allowed_tags = ['b', 'i'];
    }
    return $db->escape(trim(strip_tags($input, $allowed_tags)));
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

function get_parameter(string $name, array $options = []): string
{
    if (isset($_POST[$name])) return $_POST[$name];
    if (isset($_GET[$name])) return $_GET[$name];
    //TODO Clean up here?
    return "";
}

