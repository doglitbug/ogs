<?php
/** Wrapper for get_access_level from database
 * so we don't need to global $db on every level function
 * This is not stored as a $_SESSION variable so that access changes are instant
 * @param $user_id
 * @return string Access level Super Admin|Admin|User
 */
function get_access_level($user_id): string
{
    global $db;
    return $db->get_access_level($user_id);
}

/** Is this user a Super Admin?
 * @param $user_id
 * @return bool
 */
function is_super_admin($user_id): bool
{
    return get_access_level($user_id) == "Super Admin";
}

/** Is this user an Admin?
 * @param $user_id
 * @return bool
 */
function is_admin($user_id): bool
{
    return get_access_level($user_id) == "Admin";
}

/** Is this user a standard User?
 * @param $user_id
 * @return bool
 */
function is_user($user_id): bool
{
    return get_access_level($user_id) == "User";
}

/** This area requires a Super Admin
 * @return void
 */
function require_super_admin(): void
{
    require_login();
    if (!is_super_admin($_SESSION['user_id'])) {
        $_SESSION['message'] = "That area requires a super admin to access";
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
        $_SESSION['message'] = "That area requires an admin or higher to access";
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
 * @param string $user_id
 * @param string $garage_id
 * @return bool
 */
function is_owner(string $user_id, string $garage_id): bool
{
    global $db;
    return strcmp($db->get_user_access($user_id, $garage_id), "Owner") == 0;
}

/** IS this user a worker of the garage?
 * @param string $user_id
 * @param string $garage_id
 * @return bool
 */
function is_worker(string $user_id, string $garage_id): bool
{
    global $db;
    return strcmp($db->get_user_access($user_id, $garage_id), "Worker") == 0;
}

#endregion
?>