<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

if (isset($_GET['email'])) {
    // validate
    if (!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        $netping_error = "The email address '" . sanitize_email($_GET['email']) . "' is not valid.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
        die;
    }

    // request options
    $options = [
        'headers'     => ['Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false,
        'data_format' => 'body',
    ];

    // generate pw
    $new_pw = bin2hex(random_bytes(12));

    // parameters
    $options['body'] = wp_json_encode(['email' => sanitize_email($_GET['email']), 'password' => $new_pw, 'create_token' => 1, 'referer' => 'wp-plugin']);

    // do request
    try {
        $response = wp_remote_post(NETPING_API_URL . '/users', $options);
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
        die;
    }

    // handle
    if ($code == 201) { // account created
        update_option('netping_api_token', $body->api_token);
        update_option('netping_email', $body->email);
        update_option('netping_password', $new_pw);

        unset($netping_error);
        require plugin_dir_path(__FILE__) . '../pages/_validate_account.php';
    } elseif ($code == 422) { // validation error
        $netping_error = "This email address is already in use on netping.com. Either <a href='https://netping.com/dashboard/api' target='_blank'>login</a> to this user on netping.com to fetch it's api-token, or select another email address.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response:" . wp_remote_retrieve_body($response);
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
    }

}
