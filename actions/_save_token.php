<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');


if (isset($_GET['token'])) {

    // try to dectivate old check, it it exits
    if (get_option('netping_check_id')) {
        // same token?
        if (get_option('netping_api_token') == $_GET['token']) {
            $netping_error = "Changes ignored. You already use this token.";
            require plugin_dir_path(__FILE__) . '../pages/_plugin_settings.php';
            die;
        }
        // request options
        $options = [
            'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
            'timeout'     => 30,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.1',
            'sslverify'   => false,
            'method'      => 'PATCH',
            'data_format' => 'body',
            'body'        => wp_json_encode([
                'active' => false
            ])
        ];
        // do request
        $response = wp_remote_request('https://netping.com/api/v1/checks/' . get_option('netping_check_id'), $options );
    }

    // request options to test token
    $options = [
        'headers'     => ['Authorization' => 'Bearer ' . sanitize_text_field($_GET['token']), 'Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false
    ];

    // do request - test token
    try {
        $response = wp_remote_get(NETPING_API_URL . '/users', $options );
        $body = json_decode(wp_remote_retrieve_body($response), true);
        // get response code
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_token.php';
        die;
    }

    // handle
    if ($code == 200) { // token OK
        update_option('netping_api_token', sanitize_text_field($_GET['token']));
        update_option('netping_email', $body['email']);
        update_option('netping_password', null);

        // redirect to create_check action
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] == 'create_check') {
            require plugin_dir_path(__FILE__) . '../actions/_create_check.php';
        }
        // redirect to settings page
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] == 'plugin_settings') {
            require plugin_dir_path(__FILE__) . '../pages/_plugin_settings.php';
        }
    } else { // token not OK
        $netping_error = "This API token is not valid, or the netping account not activated.";
        // redirect back to supply_token page
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] == 'create_check') {
            require plugin_dir_path(__FILE__) . '../pages/_supply_token.php';
        }
        // redirect to settings page
        if (isset($_GET['redirect_to']) && $_GET['redirect_to'] == 'plugin_settings') {
            require plugin_dir_path(__FILE__) . '../pages/_plugin_settings.php';
        }
    }

}
