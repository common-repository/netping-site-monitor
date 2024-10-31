<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');
?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <h2>Create new account</h2>
        <?php require('_notifications.php') ?>

        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please enter an e-mail below:
        </p>
        <p class="wizard-p">
            <input style="width: 100%;" class="netping-input" id="email" placeholder="your@email.com" value="<?php echo get_option('admin_email') ?>">
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
    <div class="netping-wizard-content">
        <a href="admin.php?page=netping_supply_token">
            <button class="netping-button-left Button Button--secondary">
                <span class="netping-button-icon dashicons dashicons-arrow-left-alt"></span> Existing API-token
            </button>
        </a>
        <button onClick="send();" class="netping-button-right Button Button--secondary">Create Account
            <span class="dashicons dashicons-arrow-right-alt"></span>
        </button>
    </div>
</div>

<script>
    function send() {
        var email = document.getElementById('email').value;
        // send to action to test & save token
        window.location.replace('admin.php?page=netping_action&action=create_account&email=' + email);
    }
</script>
