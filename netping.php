<?php

/*
    Plugin Name: NEW! Wordpress Status Monitor
    Author URI: https://netping.com
    Version: 1.2.0
    Requires PHP: 5.2
    Requires at least: 4.3.0
    Author: Status Monitor
    License: GPLv2 or later
    License URI: https://www.gnu.org/licenses/gpl-2.0.html
    Description: Status Monitor Plugin for Wordpress. Check your site's status & Receive alarms.
*/

defined('ABSPATH') or die;

// auto init from wp cli
function netping_auto_init() {
    defined('NETPING_API_URL') or define('NETPING_API_URL', 'https://netping.com/api/v1');
    // variable set by wp cli
    if (get_option('netping_auto_init')) {
        // test api key
        try {
            $options = [
                'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
                'timeout'     => 30,
                'redirection' => 5,
                'blocking'    => true,
                'httpversion' => '1.1',
                'sslverify'   => false,
                'method'      => 'GET',
                'data_format' => 'body'
            ];
            $response = wp_remote_request('https://netping.com/api/v1/users', $options);
            $body = json_decode(wp_remote_retrieve_body($response));
            $code = wp_remote_retrieve_response_code($response);
            // handle error
            if ($code != 200) die('This api token is not valid.');
        } catch (Exception $e) {
            die('Could not connect to netping');
        }

        // create check
        if (get_option('netping_node_id')) {
            $node_id = (int) get_option('netping_node_id'); // use supplied node id
        } else {
            $nodes = [12, 35, 55, 62, 94, 122]; // use random node_id
            shuffle($nodes);
            $node_id = $nodes[0]; // random node
        }

        $options = [
            'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
            'timeout'     => 30,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.1',
            'sslverify'   => false,
            'data_format' => 'body',
            'body'        => wp_json_encode([
                'name'          => strlen(get_bloginfo('name')) > 20 ? substr(get_option('netping_api_token'),0,20) . "..." : get_bloginfo('name'),
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
        }

        // handle
        if ($code == 201) { // check created
            update_option('netping_check_id', $body->id);
        } else { // unknown error
            die('Could not connect to netping');
        }

        // create recipient
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
                'email' => get_option('admin_email')
            ])
        ];

        // do request
        try {
            $response = wp_remote_post(NETPING_API_URL . '/recipients', $options );
            // get response
            $body = json_decode(wp_remote_retrieve_body($response));
            $code = wp_remote_retrieve_response_code($response);
        } catch (Exception $e) {
        }

        // handle
        if ($code == 201) { // recipient created
            update_option('netping_recipient_id', $body->id);
        } else { // unknown error
            die('Could not connect to netping');
        }

        // create dashboard
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
            // create dashboard or not?
            if (!isset($body->data[0]->id)) {
                $options['body'] = wp_json_encode(['name' => 'My dashboard']);
                $response = wp_remote_post(NETPING_API_URL . '/dashboards', $options );
                $body = json_decode(wp_remote_retrieve_body($response));
                $code = wp_remote_retrieve_response_code($response);
            }
        } catch (Exception $e) {
        }

        // handle
        if ($code == 201 || $code == 200) { // dashboard created, or fetched
            if ($code == 201) update_option('netping_dashboard_id', $body->id); // created new
            if ($code == 200) update_option('netping_dashboard_id', $body->data[0]->id); // used old
        } else { // unknown error
            die('Could not connect to netping');
        }

        // create widget
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
        }

        // handle
        if ($code == 201) { // widget created
            update_option('netping_widget_id', $body->id);
        } else { // unknown error
            die('Could not connect to netping');
        }

        // create triggers
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
                die('Could not connect to netping');
            }
        } catch (Exception $e) {
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
                die('Could not connect to netping');
            }
        } catch (Exception $e) {
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
                update_option('netping_setup_complete', 1);
            } else { // error
                die('Could not connect to netping');
            }
        } catch (Exception $e) {
        }


    }
}


// deactivate
register_deactivation_hook( __FILE__, function() {
    // try to dectivate check
    if (get_option('netping_check_id')) {
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
});

// uninstall
add_action('uninstall_netping', function() {
    // try to dectivate check
    if (get_option('netping_check_id')) {
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
});

// activate
register_activation_hook( __FILE__, function() {
    // auto init from wp cli?
    $result = netping_auto_init();
    if ($result == true) goto finished;

    // test api-key
    try {
        $options = [
            'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
            'timeout'     => 30,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.1',
            'sslverify'   => false,
            'method'      => 'GET',
            'data_format' => 'body'
        ];
        $response = wp_remote_request('https://netping.com/api/v1/users', $options);
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
        // handle error
        if ($code != 200) update_option('netping_setup_complete', 0);
    } catch (Exception $e) {
        // handle error
        update_option('netping_setup_complete', 0);
    }

    // test check
    try {
        $options = [
            'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
            'timeout'     => 30,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.1',
            'sslverify'   => false,
            'method'      => 'GET',
            'data_format' => 'body'
        ];
        $response = wp_remote_request('https://netping.com/api/v1/checks/' . get_option('netping_check_id'), $options);
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
        // handle error
        if ($code != 200) {
            update_option('netping_setup_complete', 0);
        } else {
            // everything OK, make sure the check is active and polls right url...
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
                    'active'        => true,
                    'url'           => get_site_url() . '/netping-probe',
                    'metric_data'   => true
                ])
            ];
            // do request
            $response = wp_remote_request('https://netping.com/api/v1/checks/' . get_option('netping_check_id'), $options );
        }
    } catch (Exception $e) {
        // handle error
        update_option('netping_setup_complete', 0);
    }
    finished:
});

// load js & css in wp-admin
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('netpingstyles', plugins_url('/assets/netping.css?' . time(), __FILE__));          // css styles
    wp_enqueue_style('netpingbuttons', plugins_url('/assets/buttons.min.css', __FILE__));               // css buttons
    wp_enqueue_style('netpinggrid', plugins_url('/assets/bootstrap-grid.min.css', __FILE__));           // css bootstrap grid support
    wp_enqueue_script('netpingscripts', plugins_url('/assets/netping.js?' . time(), __FILE__));         // small plugin scripts
    wp_enqueue_script('netpingdates', plugins_url('/assets/moment-with-locales.js', __FILE__));         // date library used for browser compability
    wp_enqueue_script('netpingchart', plugins_url('/assets/chart.min.js', __FILE__));                   // chart.js
});

// pages
add_action('admin_menu', function() {
    // welcome / start
    add_menu_page('Netping plugin', 'Netping Status Monitor', 'manage_options', 'netping_plugin', function() {
        if (get_option('netping_setup_complete') == 1) {
            # show wp dashboard
            require_once plugin_dir_path(__FILE__) . 'pages/_dashboard.php';
        } else {
            # show setup start
            require_once plugin_dir_path(__FILE__) . 'pages/start.php';
        }

    }, 'dashicons-chart-line', null);

    // dispatch actions
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_action', function() {
        require_once plugin_dir_path(__FILE__) . 'inc/_dispatch_action.php';
    });
    // supply email
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_supply_email', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_supply_email.php';
    });
    // supply api token
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_supply_token', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_supply_token.php';
    });
    // supply node
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_supply_node', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_supply_node.php';
    });
    // redirect to dashboard
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_redirect_dashboard', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_redirect_dashboard.php';
    });
    // redirect to subscriptions
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_redirect_upgrade', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_redirect_upgrade.php';
    });
    // alerts
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_alerts', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_alerts.php';
    });
    // settings
    add_submenu_page(null, 'Login to netping', null, 'manage_options','netping_plugin_settings', function() {
        require_once plugin_dir_path(__FILE__) . 'pages/_plugin_settings.php';
    });

});

// add Settings link under Plugins
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
    $links[] = '<a href="admin.php?page=netping_plugin">Show</a>';
    return $links;
});

// catch custom url
add_action('parse_request', function () {
    if ($_SERVER["REQUEST_URI"] == '/netping-probe') {
       require_once plugin_dir_path(__FILE__) . 'probe.php';
       exit();
    }
});
