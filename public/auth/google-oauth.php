<?php
global $db;
require_once('../../private/initialize.php');

// If the captured code param exists and is valid
if (!empty($_GET['code'])) {
    // Execute cURL request to retrieve the access token
    $params = [
        'code' => $_GET['code'],
        'client_id' => $_ENV['google_oauth_client_id'],
        'client_secret' => $_ENV['google_oauth_client_secret'],
        'redirect_uri' => $_ENV['google_oauth_redirect_uri'],
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);

    // Make sure access token is valid
    if (!empty($response['access_token'])) {
        // Execute cURL request to retrieve the user info associated with the Google account
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/' . $_ENV['google_oauth_version'] . '/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);
        // Make sure the profile data exists
        if (isset($profile['email'])) {
            $google_name_parts = [];
            $google_name_parts[] = isset($profile['given_name']) ? preg_replace('/[^a-zA-Z0-9]/', '', $profile['given_name']) : '';
            $google_name_parts[] = isset($profile['family_name']) ? preg_replace('/[^a-zA-Z0-9]/', '', $profile['family_name']) : '';
            // Authenticate the user
            session_regenerate_id();
            $_SESSION['logon_method'] = 'Google';
            //Do this in a separate file to reuse code for facebook log on
            $_SESSION['email'] = $profile['email'];

            if ($db->check_email_exists($_SESSION['email'])) {
                //Get user from DB and log in
                log_in();
                $_SESSION['message'] = "Welcome back " . $_SESSION['name'];
                //TODO Close $db?
                redirect_to(url_for('auth/profile.php'));
            } else {
                echo 'Account not found, would you like to make a new one?';
                $_SESSION['name'] = implode(' ', $google_name_parts);
                //$_SESSION['picture'] = $profile['picture'] ?? '';
                //TODO Grab the Google provided details and redirect to a sign up page to allow user to choose the following:
                // username
                // name (prefilled from google)
                // email (uneditable or not shown(!) and prefilled from google)
                // location, use drop down, but google location later on
                echo '<pre>';
                var_dump($_SESSION);
                echo '</pre>';
            }
        } else {
            //TODO Close $db, or redirect to error page that does!
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        //TODO Close $db, or redirect to error page that does!
        exit('Invalid access token! Please try again later!');
    }
} else {
    // Define params and redirect to Google Authentication page
    $params = [
        'response_type' => 'code',
        'client_id' => $_ENV['google_oauth_client_id'],
        'redirect_uri' => $_ENV['google_oauth_redirect_uri'],
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    //TODO Close $db
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit;
}