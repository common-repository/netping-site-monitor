<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');


if (get_option('netping_api_token')) {
    // request options - base
    $options = [
        'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false,
        'data_format' => 'body'
    ];

    // create HTTP trigger
    try {
        // request options
        $options['body'] = wp_json_encode([
            'check_id'              => get_option('netping_check_id'),
            'name'                  => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "..." : get_bloginfo('name'),
            'type'                  => 'http',
            'value'                 => 5000,  // 5 seconds
            'metric_triggers'       => [ ['disk_free', '<', '5' ] ], // less than 5% available disk
            'minimum_duration'      => 300, // 5 minutes
            'auto_resolve_after'    => 10, // 10 minutes
            'recipients'            => [ get_option('netping_recipient_id') ]
        ]);
        // do request
        $response = wp_remote_post(NETPING_API_URL . '/checktriggers', $options);
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));

        $code = wp_remote_retrieve_response_code($response);
        // handle response
        if ($code == 201) { // trigger created
            update_option('netping_http_checktrigger_id', $body->id);
        } else { // error
            $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
            require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
            die();
        }
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
        die;
    }

    // create TLD trigger
    try {
        // request options
        $options['body'] = wp_json_encode([
            'check_id'              => get_option('netping_check_id'),
            'name'                  => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "..." : get_bloginfo('name'),
            'type'                  => 'tld',
            'days_warning'          => [2,7,30],
            'recipients'            => [ get_option('netping_recipient_id') ]
        ]);
        // do request
        $response = wp_remote_post(NETPING_API_URL . '/checktriggers', $options);
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
        // handle response
        if ($code == 201) { // trigger created
            update_option('netping_tld_checktrigger_id', $body->id);
        } else { // error
            $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
            require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
            die();
        }
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
        die;
    }

    // create SSL trigger
    try {
        // request options
        $options['body'] = wp_json_encode([
            'check_id'              => get_option('netping_check_id'),
            'name'                  => strlen(get_bloginfo('name')) > 20 ? substr(get_bloginfo('name'),0,20) . "..." : get_bloginfo('name'),
            'type'                  => 'ssl',
            'days_warning'          => [2,7,14],
            'recipients'            => [ get_option('netping_recipient_id') ]
        ]);
        // do request
        $response = wp_remote_post(NETPING_API_URL . '/checktriggers', $options);
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
        // handle response
        if ($code == 201) { // trigger created
            update_option('netping_ssl_checktrigger_id', $body->id);
        } else { // error
            $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
            require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
            die();
        }
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
        require plugin_dir_path(__FILE__) . '../pages/_supply_email.php';
        die;
    }

    // all triggers created
    $netping_message = "Setup complete!";
    update_option('netping_setup_complete', 1);
    echo('<script>window.location.replace("admin.php?page=netping_plugin");</script>');

}
