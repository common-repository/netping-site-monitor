<?php

    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    # fetch user object
    try {
        $options = [
            'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
            'timeout'     => 30,
            'redirection' => 5,
            'blocking'    => true,
            'httpversion' => '1.1',
            'sslverify'   => false,
            'data_format' => 'body',
        ];
        $response = wp_remote_get('https://netping.com/api/v1/users', $options );
        $body = wp_remote_retrieve_body($response);
        $user = json_decode($body);
        $code = wp_remote_retrieve_response_code($response);
        if ($code != 200) $netping_error = "Could not contact netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
    } catch (Exception $e) {
        $netping_error = "Could not contact netping. Please try again later";
    }

?>

<div class="netping-wizard-box" style="margin-bottom: 25px;">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <?php require('_notifications.php') ?>
    </div>
    <div class="netping-wizard-divider"></div>
    <?php require(plugin_dir_path(__FILE__) . '../inc/_menu.php'); ?>
</div>
<br>
<h2>Plugin Settings</h2>

<div class="row" style="margin-top: -10px; margin-right: 6px;">
    <div class="col-lg-6">
        <div class="netping-wizard-box">
            <div class="netping-wizard-content" style="min-height: 300px;">
                <h2>Change Account</h2>
                <p class="wizard-p">
                    If you would like to re-install your website monitoring on another netping account, paste that accounts API-token into the box below and click Re-install.
                </p>
                <p class="wizard-p">
                    <input style="width: 100%;" class="netping-input" id="token" placeholder="Your API token" value="<?php echo get_option('netping_api_token') ?>">
                </p>
                <p class="wizard-p <?php if (!get_option('netping_password')) echo "d-none"; ?>">
                    <b>Netping Credentials:</b> <?php echo get_option('netping_email')?> / <?php echo get_option('netping_password')?>
                </p>
            </div>
            <div class="netping-wizard-divider"></div>
            <div class="netping-wizard-content">
                <button onClick="send();" class="netping-button-right Button netping Button--secondary">Re-install <span class="dashicons dashicons-arrow-right-alt"></span></button>
            </div>
        </div>
    </div>

    <div class="col-lg-6 <?php if ($user->access_level != 'default') echo 'd-none'; ?>">
        <div class="netping-wizard-box">
            <div class="netping-wizard-content" style="min-height: 300px;">
                <h2>Go Premium</h2>
                <p class="wizard-p pb-4">By upgrading your Free account to an Essential account, you will get:</p>
                <p class="wizard-p">
                    <div class="points"><span class="dashicons dashicons-arrow-right-alt2"></span> <b>Personal Support:</b> contact us and we will help you setup your site monitoring the best way possible.</div>
                    <div class="points"><span class="dashicons dashicons-arrow-right-alt2"></span> <b>SMS-alerts:</b> get notified via SMS if your site has a problem.</div>
                    <div class="points"><span class="dashicons dashicons-arrow-right-alt2"></span> <b>Multiple sites:</b> Monitor up to 10 different sites on the same account.</div>
                    <div class="points"><span class="dashicons dashicons-arrow-right-alt2"></span> <b>Much more...</b></div>
                </p>
            </div>
            <div class="netping-wizard-divider"></div>
            <div class="netping-wizard-content">
                <a href="admin.php?page=netping_redirect_upgrade" target="_blank"><button class="netping-button-right Button netping Button--secondary">More information <span class="dashicons dashicons-arrow-right-alt"></span></button></a>
            </div>
        </div>
    </div>
</div>

<script>
    function send() {
        var token = document.getElementById('token').value;
        // send to action to test & save token
        window.location.replace('admin.php?page=netping_action&redirect_to=create_check&action=save_token&token=' + token);
    }
</script>
