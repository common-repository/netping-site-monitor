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
    ];

    // do requests
    try {
        // check if dashboard already exists?
        $response = wp_remote_get(NETPING_API_URL . '/dashboards', $options );
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
        // create dashboard? (don't think it will happen often, in prod)
        if (!isset($body->data[0]->id)) {
            $options['body'] = wp_json_encode(['name' => 'My dashboard']);
            $response = wp_remote_post(NETPING_API_URL . '/dashboards', $options );
            $body = json_decode(wp_remote_retrieve_body($response));
            $code = wp_remote_retrieve_response_code($response);
        }
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
        die;
    }

    // handle
    if ($code == 201 || $code == 200) { // dashboard created, or fetched
        if ($code == 201) update_option('netping_dashboard_id', $body->id); // created new
        if ($code == 200) update_option('netping_dashboard_id', $body->data[0]->id); // used old
        // do next action (create widget?)
        require plugin_dir_path(__FILE__) . '_create_widget.php';
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
    }

}
