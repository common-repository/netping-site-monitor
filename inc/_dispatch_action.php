<?php

require_once('_is_admin.php');

defined('NETPING_API_URL') or define('NETPING_API_URL', 'https://netping.com/api/v1');

// create account
if (isset($_GET['action']) && $_GET['action'] == 'create_account') {
    require_once plugin_dir_path(__FILE__) . '../actions/_create_account.php';
}

// save token
if (isset($_GET['action']) && $_GET['action'] == 'save_token') {
    require_once plugin_dir_path(__FILE__) . '../actions/_save_token.php';
}

// create check
if (isset($_GET['action']) && $_GET['action'] == 'create_check') {
    require_once plugin_dir_path(__FILE__) . '../actions/_create_check.php';
}

// create dashboard
if (isset($_GET['action']) && $_GET['action'] == 'create_dashboard') {
    require_once plugin_dir_path(__FILE__) . '../actions/_create_check.php';
}

// re-init with message
if (isset($_GET['action']) && $_GET['action'] == 'reinit_msg') {
    require_once plugin_dir_path(__FILE__) . '../actions/_reinit_with_message.php';
}
