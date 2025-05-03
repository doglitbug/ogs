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
 * @param string|null $string $string input
 * @return string
 */
function h(?string $string = ""): string
{
    return htmlspecialchars($string);
}

/** Attempt to clean up user input
 * @param string|null $input
 * @param bool $allow_html If true, allow a subset of html tags such as b and i
 *                         This generally only used with fields called 'description'
 * @return string
 * @todo allowed_html wil be set to none in settings on advice from https://www.php.net/manual/en/function.strip-tags.php
 */
function clean_input(?string $input = "", bool $allow_html = false): string
{
    global $db, $settings;
    $allowed_tags = [];
    if ($allow_html) {
        $allowed_tags = $settings->get('allowed_html');
    }
    return $db->escape(trim(strip_tags($input, $allowed_tags)));
}

/**
 * @param string $code
 * @param string $error_message
 * @param string $e
 * @return void
 */
function error(string $code = "500", string $error_message = "Error", string $e = ""): void
{
    $params['code'] = $code;
    $params['message'] = u($error_message);
    if ($_ENV['APPLICATION_ENV'] === "DEV") {
        $params['extended'] = u($e);
    }

    redirect_to(url_for('error.php?' . http_build_query($params)));
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

/** Retrieve parameter by name.
 * Tried POST, then GET, then returns empty string
 * @param string $name
 * @return string
 */
function get_parameter(string $name): string
{
    if (isset($_POST[$name])) return $_POST[$name];
    if (isset($_GET[$name])) return $_GET[$name];
    return "";
}

