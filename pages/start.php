<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');
?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <h2>Welcome to our wordpress monitoring plugin</h2>
        <?php require('_notifications.php') ?>
        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            This guide will help you setup free monitoring for your wordpress site
        </p>
        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please choose if you would like to create a <b>new netping account</b>, our use an <b>already existing netping API-token</b>.
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
    <div class="netping-wizard-content">
        <a href="admin.php?page=netping_supply_token"><button role="button" class="Button netping-button-left Button--secondary"><span class="netping-button-icon dashicons dashicons-admin-generic"></span> Existing API-token</button></a>
        <a href="admin.php?page=netping_supply_email"><button role="button" class="Button netping-button-right Button--secondary"><span class="netping-button-icon dashicons dashicons-plus"></span> Create new account</button></a>
    </div>
</div>
