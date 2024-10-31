<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    $options = [
        'headers'     => ['Authorization' => 'Bearer ' . get_option('netping_api_token'), 'Content-Type' => 'application/json'],
        'timeout'     => 30,
        'redirection' => 5,
        'blocking'    => true,
        'httpversion' => '1.1',
        'sslverify'   => false
    ];

    // do request
    try {
        $response = wp_remote_get(NETPING_API_URL . '/checks/' . get_option('netping_check_id'), $options );
        // get response
        $body = json_decode(wp_remote_retrieve_body($response));
        $code = wp_remote_retrieve_response_code($response);
    } catch (Exception $e) {
        $netping_error = "There was a problem connecting to netping. Please try again later.";
    }

    // handle
    if ($code == 200) { // check fetched
    } else { // unknown error
        $netping_error = "There was a problem connecting to netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
    }

?>

<style>
.dashicons-yes {
    font-size: 32px !important;
    color: green;
    margin-right: 15px;
    position: relative;
    top: -7px;
}
</style>

<div class="netping-wizard-box" style="margin-bottom: 25px;">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <?php require('_notifications.php') ?>
    </div>
    <div class="netping-wizard-divider"></div>
    <?php require(plugin_dir_path(__FILE__) . '../inc/_menu.php'); ?>
</div>
<br>
<h2>Active Alert Triggers</h2>
<p>To edit or add triggers, please visit the Netping Settings</b>.</p>

<table id="triggers" class="netping_table">
    <tr>
        <td>Type</td>
        <td>Condition</td>
        <td>Alert Recipient(s)</td>
    </tr>
    <tr>
        <td>-</td>
        <td>-</td>
        <td>-</td>
    </tr>
</table>

<script>

        var triggers = <?php echo json_encode($body->triggers->data) ?>;
        // process triggers
        triggers.forEach(function(trigger) {
            
            // HTTP trigger
            if (trigger.type == 'http') {
                // uptime trigger
                var condition = "If site responsetime > " + trigger.value + " ms (or unresponsive) for longer than " + (trigger.minimum_duration / 60) + " minutes.";
                var table = document.getElementById("triggers");
                table.deleteRow(1);
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                cell1.innerHTML = "<span class='dashicons dashicons-yes'></span>Responsetime Trigger";
                cell2.innerHTML = condition;
                // recipients
                trigger.recipients.data.forEach(function(recipient) {
                    // handle different types
                    if (recipient.type == 'email') cell3.innerHTML = recipient.email + " (email)";
                    if (recipient.type == 'sms') cell3.innerHTML = recipient.msisdn + " (sms)";
                    if (recipient.type == 'list') cell3.innerHTML = recipient.name + " (priority list)";
                    if (recipient.type == 'schedule') cell3.innerHTML = recipient.name + " (schedule)";
                    if (recipient.type == 'app') cell3.innerHTML = recipient.email + " (phone app)";
                    if (recipient.type == 'slack') cell3.innerHTML = recipient.channel + " (slack channel)";
                    if (recipient.type == 'webhook') cell3.innerHTML = recipient.url + " (webhook)";
                });

                // metric triggers
                var metric_triggers = trigger.metric_triggers ?? [];
                metric_triggers.forEach(function(metric) {
                    var row = table.insertRow();
                    var cell1 = row.insertCell(0);
                    var cell2 = row.insertCell(1);
                    var cell3 = row.insertCell(2);
                    cell1.innerHTML = "<span class='dashicons dashicons-yes'></span>Resource Trigger";
                    cell2.innerHTML = "If " + metric[0] + " " + metric[1] + " " + metric[2] + " for longer than " + (trigger.minimum_duration / 60) + " minutes.";
                    // add recipients
                    trigger.recipients.data.forEach(function(recipient) {
                        // handle different types
                        if (recipient.type == 'email') cell3.innerHTML = recipient.email + " (email)";
                        if (recipient.type == 'sms') cell3.innerHTML = recipient.msisdn + " (sms)";
                        if (recipient.type == 'list') cell3.innerHTML = recipient.name + " (priority list)";
                        if (recipient.type == 'schedule') cell3.innerHTML = recipient.name + " (schedule)";
                        if (recipient.type == 'app') cell3.innerHTML = recipient.email + " (phone app)";
                        if (recipient.type == 'slack') cell3.innerHTML = recipient.channel + " (slack channel)";
                        if (recipient.type == 'webhook') cell3.innerHTML = recipient.url + " (webhook)";
                    });
                });
            }

            // tld
            if (trigger.type == 'tld') {
                var table = document.getElementById("triggers");
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                cell1.innerHTML = "<span class='dashicons dashicons-yes'></span>Domain Expiry Trigger";
                cell2.innerHTML = "Send a warning on days " + trigger.days_warning + " to expiry.";
                // add recipients
                trigger.recipients.data.forEach(function(recipient) {
                    // handle different types
                    if (recipient.type == 'email') cell3.innerHTML = recipient.email + " (email)";
                    if (recipient.type == 'sms') cell3.innerHTML = recipient.msisdn + " (sms)";
                    if (recipient.type == 'list') cell3.innerHTML = recipient.name + " (priority list)";
                    if (recipient.type == 'schedule') cell3.innerHTML = recipient.name + " (schedule)";
                    if (recipient.type == 'app') cell3.innerHTML = recipient.email + " (phone app)";
                    if (recipient.type == 'slack') cell3.innerHTML = recipient.channel + " (slack channel)";
                    if (recipient.type == 'webhook') cell3.innerHTML = recipient.url + " (webhook)";
                });
            }

            // ssl
            if (trigger.type == 'ssl') {
                var table = document.getElementById("triggers");
                var row = table.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                cell1.innerHTML = "<span class='dashicons dashicons-yes'></span>SSL Expiry Trigger";
                cell2.innerHTML = "Send a warning on days " + trigger.days_warning + " to expiry.";
                // add recipients
                trigger.recipients.data.forEach(function(recipient) {
                    // handle different types
                    if (recipient.type == 'email') cell3.innerHTML = recipient.email + " (email)";
                    if (recipient.type == 'sms') cell3.innerHTML = recipient.msisdn + " (sms)";
                    if (recipient.type == 'list') cell3.innerHTML = recipient.name + " (priority list)";
                    if (recipient.type == 'schedule') cell3.innerHTML = recipient.name + " (schedule)";
                    if (recipient.type == 'app') cell3.innerHTML = recipient.email + " (phone app)";
                    if (recipient.type == 'slack') cell3.innerHTML = recipient.channel + " (slack channel)";
                    if (recipient.type == 'webhook') cell3.innerHTML = recipient.url + " (webhook)";
                });
            }

        });

</script>
