<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');


if (get_option('netping_api_token')) {
    // request options
    $options = [
        'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false,
        'data_format' => 'body',
        'body'        => wp_json_encode([
            'name'  => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "... admin" : get_bloginfo('name') . " admin",
            'type'  => 'email',
            'email' => get_option('netping_email')
        ])
    ];

    // do request
    try {
        $response = wp_remote_post(NETPING_API_URL . '/recipients', $options );
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
        die;
    }

    // handle
    if ($code == 201) { // recipient created
        update_option('netping_recipient_id', $body->id);
        require plugin_dir_path(__FILE__) . '_create_trigger.php';
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
    }

}
