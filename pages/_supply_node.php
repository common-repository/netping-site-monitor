<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    # fetch list of nodes
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
        $response = wp_remote_get('https://netping.com/api/v1/nodes', $options );
        $body = wp_remote_retrieve_body($response);
        $nodes = json_decode($body);
        $code = wp_remote_retrieve_response_code($response);
        if ($code != 200) {
            $netping_error = "Could not contact netping. Please try again later. Response: " . wp_remote_retrieve_body($response);
        }
    } catch (Exception $e) {
        $netping_error = "Could not contact netping. Please try again later";
    }

    # activation message?
    if (isset($_GET['just_activated'])) $netping_message = "Your account was activated correctly!";

?>

<div class="netping-wizard-box">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <h2>Choose monitoring location</h2>
        <?php require('_notifications.php') ?>

        <p class="wizard-p">
            <span class="dashicons dashicons-arrow-right-alt2"></span>
            Please select at what location you would like to check your site <b>from:</b>
        </p>
        <p class="wizard-p">
            <select id="node_id" style="width: 350px;">
                <?php
                    foreach ($nodes->data as $n) {
                        echo('<option value="' . $n->id . '">' . $n->name . '</option>');
                    }
                ?>
            </select>
        </p>
    </div>
    <div class="netping-wizard-divider"></div>
    <div class="netping-wizard-content">
        <button onClick="send();" id="the_button" role="button" class="Button netping-button-right Button--secondary">Next <span class="dashicons dashicons-arrow-right-alt"></span></button>
    </div>
</div>

<script>
    function send() {
        document.getElementById("the_button").disabled = true;
        var node_id = document.getElementById('node_id').value;
        window.location.replace('admin.php?page=netping_action&action=create_check&node_id=' + node_id);
    }
</script>
