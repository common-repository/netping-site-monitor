<?php

    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    // green message
    if (isset($netping_message))  {
        echo('<div class="netping-message"><span class="dashicons dashicons-saved"></span>' . sanitize_text_field($netping_message) . '</div>');
    }

    // red message
    if (isset($netping_error)) {
        echo('<div class="netping-error"><span class="dashicons dashicons-warning"></span>' . sanitize_text_field($netping_error) . '</div>');
    }
