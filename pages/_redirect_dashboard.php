<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');
?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <?php require('_notifications.php') ?>
        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please wait...
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
</div>

<script>
    window.location.replace("https://netping.com/api/v1/users?login=true&api_token=<?php echo get_option('netping_api_token')?>");
</script>
