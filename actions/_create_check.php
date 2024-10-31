<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    // fetch
    if (isset($_GET['node_id']) && is_numeric($_GET['node_id'])) {
        $node_id = $_GET['node_id'];
    } else {
        $nodes = [12, 35, 55, 62, 94, 122];
        shuffle($nodes);
        $node_id = $nodes[0]; // random node
    }

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
            'name'          => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "..." : get_bloginfo('name'),
            'interval'      => 180,
            'type'          => 'http',
            'method'        => 'get',
            'active'        => true,
            'node_id'       => (int) $node_id,
            'url'           => get_site_url() . '/netping-probe',
            'metric_data'   => true
        ])
    ];

    // do request
    try {
        $response = wp_remote_post(NETPING_API_URL . '/checks', $options );
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/start.php';
        die;
    }

    // handle
    if ($code == 201) { // check created
        update_option('netping_check_id', $body->id);
        require plugin_dir_path(__FILE__) . '_create_dashboard.php';
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
        require plugin_dir_path(__FILE__) . '../pages/start.php';
    }
