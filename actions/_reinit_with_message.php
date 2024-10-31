<?php

require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');


if (isset($_GET['type'])) {

    // check missing - reconfigure
    if ($_GET['type'] == 'check') {
        update_option('netping_setup_complete', 0);
        $netping_error = "Your configured netping check seems to have been deleted. Please re-initialize by following this guide.";
        require plugin_dir_path(__FILE__) . '../pages/start.php';
        die;
    }

    // api token not valid - reconfigure
    if ($_GET['type'] == 'api_token') {
        update_option('netping_setup_complete', 0);
        $netping_error = "Your configured netping API token seems to have changed. Please re-initialize by following this guide.";
        require plugin_dir_path(__FILE__) . '../pages/start.php';
        die;
    }

}
