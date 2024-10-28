<?php

function is_super_admin(): bool
{
    return true;
}

function is_admin(): bool
{
    return true;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function require_super_admin(): void
{
    if (!is_super_admin()) {
        $_SESSION['message'] = "This area requires a super admin to access";
        redirect_to(url_for('auth/login.php'));
    }
}

function require_admin(): void
{
    if (!is_super_admin() && !is_admin()) {
        $_SESSION['message'] = "This area requires an admin to access";
        redirect_to(url_for('auth/login.php'));
    }
}

function require_login(): void
{
    //TODO Add redirect to for after after log in?
    //$_SESSION['redirect'] = h("//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}");
    if (!is_logged_in()) {
        $_SESSION['message'] = "This area requires being logged in";
        redirect_to(url_for('auth/login.php'));
    }
}

function log_out(): void
{
    unset($_SESSION['user_id']);
    session_destroy();
}

?>