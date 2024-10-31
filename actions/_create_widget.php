<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');


if (get_option('netping_api_token')) {
    // request options
    $data_sources = [
        [
            'id' => get_option('netping_check_id'),
            'name' => 'Responsetime',
            'type' => 'responsetime',
            'style' => 'solid',
            'color' => 0
        ],
        [
            'id' => get_option('netping_check_id'),
            'name' => 'Free disk (%)',
            'type' => 'disk_free',
            'style' => 'solid',
            'color' => 9
        ],
        [
            'id' => get_option('netping_check_id'),
            'name' => 'Free memory (%)',
            'type' => 'mem_free',
            'style' => 'solid',
            'color' => 2
        ],
        [
            'id' => get_option('netping_check_id'),
            'name' => 'CPU Usage (%)',
            'type' => 'cpu_usage',
            'style' => 'solid',
            'color' => 3
        ]
    ];
    $options = [
        'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false,
        'data_format' => 'body',
        'body'        => wp_json_encode([
            'dashboard_id'  => get_option('netping_dashboard_id'),
            'type'          => 'uptime_diagram',
            'title'         => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "..." : get_bloginfo('name'),
            'x'             => 0,
            'y'             => 0,
            'w'             => 12,
            'h'             => 6,
            'time_period'   => '1h',
            'data_sources'  => $data_sources
        ])
    ];

    // do request
    try {
        $response = wp_remote_post(NETPING_API_URL . '/widgets', $options );
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
        die;
    }

    // handle
    if ($code == 201) { // widget created
        update_option('netping_widget_id', $body->id);
        require plugin_dir_path(__FILE__) . '_create_recipient.php';
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
        require plugin_dir_path(__FILE__) . '../pages/_supply_node.php';
    }

}
