<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');
?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <h2>Initializing</h2>
        <?php require('_notifications.php') ?>

        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please wait a few seconds....
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
    <div class="netping-wizard-content" style="height: 83px !important;">
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    </div>
</div>

<script>

    Http = new XMLHttpRequest();
    url = 'https://netping.com/api/v1/users';
    Http.open("GET", url);
    Http.setRequestHeader("Authorization", 'Bearer <?php echo get_option('netping_api_token')?>');
    Http.onreadystatechange = function() {
        if (Http.readyState === XMLHttpRequest.DONE && Http.status == 200) {
            //location.href = 'admin.php?page=netping_supply_node&just_activated=1';
            window.location.replace('admin.php?page=netping_action&action=create_check');
        }
    }
    Http.send();

</script>
