<?php
    require_once(plugin_dir_path(__FILE__) . '../inc/_is_admin.php');

    function OSName() {
        if (false == function_exists("shell_exec") || false == is_readable("/etc/os-release")) {
            return null;
        }
        $os = shell_exec('cat /etc/os-release | grep "PRETTY_NAME"');
        return explode("=", $os)[1];
    }

    // show readable format from seconds integer
    function secondsToTime($seconds) {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }

    // get mysql uptime
    global $wpdb;
    $results = $wpdb->get_results( "SHOW GLOBAL STATUS LIKE 'Uptime'" );
    if (isset($results[0]->Value)) {
        $mysql_uptime = $results[0]->Value;
    }

    // get db name
    $results = $wpdb->get_results( "SELECT DATABASE() as dbname" );
    if (isset($results[0]->dbname)) {
        $mysql_dbname = $results[0]->dbname;
    }

    // get db size
    $results = $wpdb->get_results( 'SELECT table_schema AS "Database", ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS "Size (MB)" FROM information_schema.TABLES GROUP BY table_schema' );
    if (isset($results[0]->{'Size (MB)'})) {
        $mysql_dbsize = $results[0]->{'Size (MB)'};
    }

?>

<div class="netping-wizard-box" style="margin-bottom: 20px;">
    <div class="netping-wizard-content">
        <img class="netping-logo" src="<?php echo plugins_url('../assets/logo.svg', __FILE__)?>">
        <?php require('_notifications.php') ?>
    </div>
    <div class="netping-wizard-divider"></div>
    <?php require(plugin_dir_path(__FILE__) . '../inc/_menu.php'); ?>
</div>
<div style="padding-right: 15px; margin-top: -25px;">
    <canvas id="metric_chart" style="width: 99%; height: 400px;"></canvas>
</div>

<div id="spinner" style="text-align: center;">
    <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
    <br>Loading...
</div>

<h2>Server Metrics</h2>

<table class="netping_metrics_table" cellspacing=0 cellpadding=0>
    <tr>
        <td>
            Memory Used
            <small>Webserver RAM</small>
        </td>
        <td colspan="3">
            <div id="mem_used" class="percentageFill">0%</div>
            <center><small id="mem_text">-</small></center>
        </td>
    </tr>
    <tr>
        <td>
            Storage Used<br>
            <small>On the volume your WP site is located on.</small>
        </td>
        <td colspan="3">
            <div id="storage_used" class="percentageFill">0%</div>
            <center><small id="storage_text">-</small></center>
        </td>
    </tr>
    <tr>
        <td>
            CPU Used
            <small>Based on all cpu cores on this machine.</small>
        </td>
        <td colspan="3">
            <div id="cpu_used" class="percentageFill">0%</div>
        </td>

    </tr>
    <tr>
        <td>
            CPU Info
            <small>Server CPU cores and architecture.</small>
        </td>
        <td colspan="3">
            <?php
                if (is_readable('/proc/cpuinfo')) {
                    $cpuinfo = file_get_contents('/proc/cpuinfo');
                    preg_match_all('/^processor/m', $cpuinfo, $matches);
                    $file = file('/proc/cpuinfo');
                    $proc_details = $file[4];
                    echo count($matches[0]) . ' x ' . substr($proc_details, 13);
                } else {
                    echo "-";
                }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            Server Uptime
            <small>Server system uptime (since reboot)</small>
        </td>
        <td>
            <?php
            $result = shell_exec('uptime -p');
            if ($result) {
                echo $result;
            } else {
                echo "-";
            }
            ?>
        </td>
        <td class="big">
            OS version
            <small>OS type & version</small>
        </td>
        <td>
            <?php
                $os = OSName();
                if ($os) {
                    echo trim(trim($os), '"');
                } else {
                    echo "-";
                }
            ?>
        </td>
    </tr>

</table>

<br><br>
<h2>Http Check</h2><br>

<table class="netping_metrics_table2" cellspacing=0 cellpadding=0>
    <tr>
        <td>
            Checked From
            <small>Where are your site checked from.</small>
        </td>
        <td id="checked_from">-</td>
        <td>
            Last Checked
            <small>Site last checked (happens every 3 minutes)</small>
        </td>
        <td id="checked_at">-</td>
    </tr>
    <tr>
        <td>
            Domain Expiry
            <small>At this date do your domain name expire.</small>
        </td>
        <td id="domain_expiry">-</td>
        <td>
            SSL Expiry
            <small>At this date do your SSL-certificate expire.</small>
        </td>
        <td id="ssl_expiry">-</td>
    </tr>
</table>

<br><br>
<h2>Database Information</h2>
<br>

<table class="netping_metrics_table2" cellspacing=0 cellpadding=0>
    <tr>
        <td>
            MySQL Uptime
            <small>DB server uptime.</small>
        </td>
        <td>
            <?php
                if (isset($mysql_uptime)) {
                    echo secondsToTime($mysql_uptime);
                } else {
                    echo "-";
                }
            ?>
        </td>
        <td>
            Database name & size
            <small>Your WP database name & size.</small>
        </td>
        <td>
            <?php
                if (isset($mysql_dbname) && isset($mysql_dbsize)) {
                    echo $mysql_dbname . " (" . $mysql_dbsize . " MB)";
                } else {
                    echo "-";
                }
            ?>
        </td>
    </tr>
</table>

<script>
    // declare global
    result = null;
    last_result = null;

    // diagram options
    var config = {
        type: 'line',
        data: {
            datasets: [{
                label: 'CPU used',
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                fill: false
            },
            {
                label: 'RAM used',
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                fill: false
            },
            {
                label: 'Storage used',
                backgroundColor: window.chartColors.green,
                borderColor: window.chartColors.green,
                fill: false
            }],
        },
        options: {
            responsive: true,
            events: [],
            title: {
                display: true,
                text: ''
            },
            tooltips: {
              intersect: false
            },
            scales: {
                yAxes: [{
                    position: 'right',
                    gridLines: {
                        drawBorder: false,

                    },
                    ticks: {
                        min: 0,
                        max: 100,
                        stepSize: 10,
                        callback: function(value, index, values) {
                            return value + " %";
                        }
                    }
                }]
            },
            legend: {
                position: "top",
                align: "start",
                labels: {
                    boxWidth: 10
                }
            }
        }
    };

    // init diagram
    var ctx = document.getElementById('metric_chart').getContext('2d');
    chart = new Chart(ctx, config);

    // fill chart with values
    for (i = 0; i < 60; i++) {
      chart.data.labels.push('');
      chart.data.datasets[0].data.push(0);
      chart.data.datasets[1].data.push(0);
      chart.data.datasets[2].data.push(0);
    }
    chart.update();

    // read data from probe script
    setInterval(function(){
        Http = new XMLHttpRequest();
        url = '/netping-probe';
        Http.open("GET", url);
        Http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        Http.onreadystatechange = function() {
            if (Http.readyState === XMLHttpRequest.DONE && Http.status == 200) {
                last_result = result;
                try {
                    result = JSON.parse(Http.response);
                    if (result && last_result) {
                        // add x label
                        chart.data.labels.shift();
                        chart.data.labels.push('');
                        // calculate cpu usage
                        var total_cpu_s = (result.cpu_time_user - last_result.cpu_time_user) +
                                          (result.cpu_time_nice - last_result.cpu_time_nice) +
                                          (result.cpu_time_sys - last_result.cpu_time_sys) +
                                          (result.cpu_time_idle - last_result.cpu_time_idle) +
                                          (result.cpu_time_iowait - last_result.cpu_time_iowait) +
                                          (result.cpu_time_irq - last_result.cpu_time_irq) +
                                          (result.cpu_time_softirq - last_result.cpu_time_softirq) +
                                          (result.cpu_time_steal - last_result.cpu_time_steal) +
                                          (result.cpu_time_guest - last_result.cpu_time_guest) +
                                          (result.cpu_time_guest_nice - last_result.cpu_time_guest_nice);
                        var idle_cpu_s = (result.cpu_time_idle - last_result.cpu_time_idle) + (result.cpu_time_steal -  last_result.cpu_time_steal);
                        var cpu_used = 100 - ((idle_cpu_s / total_cpu_s) * 100);

                        // add to diagram
                        chart.data.datasets[0].data.shift();
                        chart.data.datasets[0].data.push(parseFloat(cpu_used.toFixed(2)));
                        chart.data.datasets[1].data.shift();
                        chart.data.datasets[1].data.push(parseFloat(100 - result.mem_free));
                        chart.data.datasets[2].data.shift();
                        chart.data.datasets[2].data.push(parseFloat(100 - result.disk_free));

                        // add to table

                        // cpu
                        if (cpu_used) {
                            // color
                            document.getElementById('cpu_used').style.background = 'linear-gradient(to right, ' + window.chartColors.red  + ' ' + parseInt(cpu_used) + '%, lightgray ' + parseInt(cpu_used) + '%)';
                            // percentage text
                            document.getElementById('cpu_used').innerHTML = parseInt(cpu_used) + '%';
                        }
                        // memory
                        if (result.mem_total_mb) {
                            // color
                            document.getElementById('mem_used').style.background = 'linear-gradient(to right, ' + window.chartColors.blue  + ' ' + (100 - parseInt(result.mem_free)) + '%, lightgray ' + (100 - parseInt(result.mem_free)) + '%)';
                            // percentage text
                            document.getElementById('mem_used').innerHTML = (100 - parseInt(result.mem_free)) + '%';
                            // text field
                            document.getElementById('mem_text').innerHTML =  parseInt(result.mem_total_mb - result.mem_free_mb) + " MB used (of " + parseInt(result.mem_total_mb) + " MB total)";
                        }
                        // storage
                        if (result.disk_total_mb) {
                            // color
                            document.getElementById('storage_used').style.background = 'linear-gradient(to right, ' + window.chartColors.green  + ' ' + (100 - parseInt(result.disk_free)) + '%, lightgray ' + (100 - parseInt(result.disk_free)) + '%)';
                            // percentage text
                            document.getElementById('storage_used').innerHTML = (100 - parseInt(result.disk_free)) + '%';
                            // text field
                            document.getElementById('storage_text').innerHTML =  parseInt((result.disk_total_mb - result.disk_free_mb) / 1024) + " GB used (of " + parseInt(result.disk_total_mb / 1024) + " GB total)";
                        }
                    }
                    chart.update();
                } catch (e) {}
                // hide spinner
                document.getElementById('spinner').style.display = 'none';
            }
        }
        Http.send();
    }, 500);

    function update_check () {
        try {
            // fill in check info
            Http2 = new XMLHttpRequest();
            Http2.open("GET", 'https://netping.com/api/v1/checks/<?php echo get_option('netping_check_id')?>' );
            Http2.setRequestHeader("Content-Type", "application/json");
            Http2.setRequestHeader("Authorization", "Bearer <?php echo get_option('netping_api_token')?>");
            Http2.onreadystatechange = function() {
                if (Http2.readyState === XMLHttpRequest.DONE && Http2.status == 200) {
                    result2 = JSON.parse(Http2.response);
                    // fill in & convert dates to browsers timezone
                    if (result2.checked_at) {
                        //document.getElementById('checked_at').innerHTML = new Date(result2.checked_at).toLocaleString({ timeZone: 'UTC'});
                        document.getElementById('checked_at').innerHTML = moment.utc(result2.checked_at).format("YYYY-MM-DD HH:mm:ss") + " (" + moment.utc().diff(moment.utc(result2.checked_at), 'seconds') + " seconds ago)";
                    }
                    if (result2.domain_expiry) {
                        document.getElementById('domain_expiry').innerHTML = moment.utc(result2.domain_expiry).format("YYYY-MM-DD HH:mm:ss") + " (in " + moment.utc(result2.domain_expiry).diff(moment.utc(), 'days') + " days)";
                    }
                    if (result2.ssl_expiry) {
                        document.getElementById('ssl_expiry').innerHTML = moment.utc(result2.ssl_expiry).format("YYYY-MM-DD HH:mm:ss") + " (in " + moment.utc(result2.ssl_expiry).diff(moment.utc(), 'days') + " days)";
                    }
                    // get node info
                    Http2 = new XMLHttpRequest();
                    Http2.open("GET", 'https://netping.com/api/v1/nodes/' + result2.node_id );
                    Http2.setRequestHeader("Content-Type", "application/json");
                    Http2.setRequestHeader("Authorization", "Bearer <?php echo get_option('netping_api_token')?>");
                    Http2.onreadystatechange = function() {
                        if (Http2.readyState === XMLHttpRequest.DONE && Http2.status == 200) {
                            result2 = JSON.parse(Http2.response);
                            document.getElementById('checked_from').innerHTML = result2.name;
                        }
                    }
                    Http2.send();

                }
                // check does not exist anymore
                if (Http2.readyState === XMLHttpRequest.DONE && Http2.status == 404) {
                    window.location.replace("admin.php?page=netping_action&action=reinit_msg&type=check");
                }
                // api key not working
                if (Http2.readyState === XMLHttpRequest.DONE && Http2.status == 401) {
                    window.location.replace("admin.php?page=netping_action&action=reinit_msg&type=api_token");
                }
            }
            Http2.send();
        } catch (e) {
        }
    }

    // update check + every 10 seconds
    update_check();
    setInterval(function(){
        update_check();
    }, 60000);

</script>
