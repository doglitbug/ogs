<?php
/** Is this user a Super Admin?
 * @param $user_id
 * @return bool
 */
function is_super_admin($user_id): bool
{
    global $db;
    return $db->get_access_level($user_id) == "Super Admin";
}

/** Is this user an Admin?
 * @param $user_id
 * @return bool
 */
function is_admin($user_id): bool
{
    global $db;
    return $db->get_access_level($user_id) == "Admin";
}

/** Is this user a standard User?
 * @param $user_id
 * @return bool
 */
function is_user($user_id): bool
{
    global $db;
    return $db->get_access_level($user_id) == "User";
}

/** This area requires a Super Admin
 * @return void
 */
function require_super_admin(): void
{
    require_login();
    if (!is_super_admin($_SESSION['user_id'])) {
        $_SESSION['error'] = "That area requires a super admin to access";
        //TODO Why here?
        redirect_to(url_for('auth/login.php'));
    }
}

/** This area requires an Admin or higher
 * @return void
 */
function require_admin(): void
{
    require_login();
    if (!is_super_admin($_SESSION['user_id']) && !is_admin($_SESSION['user_id'])) {
        $_SESSION['error'] = "That area requires an admin or higher to access";
        //TODO Why here?
        redirect_to(url_for('auth/login.php'));
    }
}

/** This area requires a User to be logged in
 * @return void
 */
function require_login(): void
{
    //TODO Add redirect to for after after log in?
    //$_SESSION['redirect'] = h("//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    if (!is_logged_in()) {
        $_SESSION['message'] = "That area requires being logged in";
        redirect_to(url_for('auth/login.php'));
    }
}

#region ownership
/** Is this user an owner of the garage?
 * @param string $garage_id
 * @return bool
 */
function is_owner(string $garage_id): bool
{
    global $db;
    if (!is_logged_in()) return false;

    return strcmp($db->get_user_access($_SESSION['user_id'], $garage_id), "Owner") == 0;
}

/** Is this user a worker of the garage?
 * @param string $garage_id
 * @return bool
 */
function is_worker(string $garage_id): bool
{
    global $db;
    if (!is_logged_in()) return false;

    return strcmp($db->get_user_access($_SESSION['user_id'], $garage_id), "Worker") == 0;
}

/** Is the current user an owner or worker of the garage?
 * @param array $garage
 * @return bool
 */
function can_edit_items(array $garage): bool {
    if (!is_logged_in()) return false;

    return (is_owner($garage['garage_id']) || is_worker($garage['garage_id']));
}

/** Is the current user an owner or worker of the item?
 * If no user logged in, return false
 * @param array $item
 * @return bool
 */
function can_edit_item(array $item): bool {
    if (!is_logged_in()) return false;

    return (is_owner($item['garage_id']) || is_worker($item['garage_id']));
}

#endregion
?>