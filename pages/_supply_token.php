<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');
?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <h2>Supply API-token</h2>
        <?php require('_notifications.php') ?>

        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please enter your netping API-token below:
        </p>
        <p class="wizard-p">
            <input style="width: 100%;" class="netping-input" id="token" placeholder="Your API token" value="<?php echo get_option('netping_api_token') ?>">
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
    <div class="netping-wizard-content">
        <a href="admin.php?page=netping_supply_email">
            <button role="button" class="Button netping-button-left Button--secondary"><span class="netping-button-icon dashicons dashicons-arrow-left-alt"></span> Create New Account</button>
        </a>
        <button onClick="send();" class="Button netping-button-right Button--secondary">Use token <span class="dashicons dashicons-arrow-right-alt"></span></button>
    </div>
</div>

<script>
    function send() {
        var token = document.getElementById('token').value;
        // send to action to test & save token
        window.location.replace('admin.php?page=netping_action&redirect_to=create_check&action=save_token&token=' + token);
    }
</script>
