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
 * @param string $string input
 * @return string output
 */
function u(string $string = ""): string
{
    return urlencode($string);
}

/** Apply rawurlencode to provided value
 * @param string $string input
 * @return string output
 */
function raw_u(string $string = ""): string
{
    return rawurlencode($string);
}

/** Apply htmlspecialchars to provided value
 * @param string $string input
 * @return string
 */
function h(string $string = ""): string
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
 * @param string $name message/error
 * @return void
 */
function print_and_delete(string $name = ""): void
{
    if ($name != "" && isset($_SESSION[$name]) && $_SESSION[$name] != '') {
        echo '<div id="' . $name . '">' . h($_SESSION[$name]) . '</div>';
        unset($_SESSION[$name]);
    }
}