<?php

// init
header('Content-Type: application/json');
error_reporting(0);
$result = [];
$result['metrics-type'] = "netping-probe";

// ram method 1
try {
    if (is_readable('/proc/meminfo')) {
        $fh = fopen('/proc/meminfo','r');
        if ($fh) {
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                    $result['mem_total_mb'] = (int)($pieces[1] / 1024);
                }
                if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
                    $result['mem_free_mb'] = (int)($pieces[1] / 1024);
                }
                if (isset($result['mem_total_mb']) && isset($result['mem_free_mb'])) {
                    $result['mem_free'] = (float) number_format((float) (($result['mem_free_mb'] / $result['mem_total_mb']) * 100), 2);
                }
            }
            fclose($fh);
        }
    }
} catch (Exception $e) {
}

// ram method 2
if (!isset($result['mem_total_mb'])) {
    try {
        $free = shell_exec('free');
        if ($free) {
            $free = (string)trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(" ", $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            $result['mem_total_mb'] = (int) ($mem[1] / 1024);
            $result['mem_free_mb'] = (int) ($mem[3] / 1024);
            $result['mem_free'] = (float) number_format((float) (($result['mem_free_mb'] / $result['mem_total_mb']) * 100), 2);
        }
    } catch (Exception $e) {
    }
}

// disk method 1
try {
    $dir = getcwd();
    if ($dir) {
        $result['disk_total_mb'] = (int) ((disk_total_space($dir) / 1024) / 1024);
        $result['disk_free_mb'] = (int) ((disk_free_space($dir) / 1024) / 1024);
        $result['disk_free'] = (float) number_format((float) (($result['disk_free_mb'] / $result['disk_total_mb']) * 100), 2);
    }
} catch (Exception $e) {
}

// cpu - proc/stat
try {
    if (is_readable('/proc/stat')) {
        $stat = file('/proc/stat');
        $info = explode(" ", preg_replace("!cpu +!", "", $stat[0]));
        $result['cpu_time_user'] = (int) $info[0];
        $result['cpu_time_nice'] = (int) $info[1];
        $result['cpu_time_sys'] = (int) $info[2];
        $result['cpu_time_idle'] = (int) $info[3];
        $result['cpu_time_iowait'] = (int) $info[4];
        $result['cpu_time_irq'] = (int) $info[5];
        $result['cpu_time_softirq'] = (int) $info[6];
        $result['cpu_time_steal'] = (int) $info[7];
        $result['cpu_time_guest'] = (int) $info[8];
        $result['cpu_time_guest_nice'] = (int) $info[9];
    }
} catch (Exception $e) {
}

echo json_encode($result);
