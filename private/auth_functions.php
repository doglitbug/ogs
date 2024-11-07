<?php
/** Is there a logged-in user?
 * @return bool
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Look at $_SESSION['email'] and log in that user.
 * Assume that this user exists in database!
 * @return void
 */
function log_in(): void
{
    //TODO Remove location or location_id
    //Included here to provide a default location for new garages
    //TODO Log in by id???
    global $db;
    $user = $db->get_user_by_email($_SESSION['email']);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['location_id'] = $user['location_id'];
    $_SESSION['location'] = $user['location'];
}

/** Log out the currently logged-in user
 * Unset all $_SESSION variables set by log_in
 * @return void
 */

function log_out(): void
{
    unset($_SESSION['user_id']);
    unset($_SESSION['email']);
    unset($_SESSION['username'] );
    unset($_SESSION['name']);
    unset($_SESSION['location']);
    session_destroy();
}