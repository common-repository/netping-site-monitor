<?php

defined('ABSPATH') or die;

defined('NETPING_API_URL') or define('NETPING_API_URL', 'https://netping.com/api/v1');

// admin or die
$user = wp_get_current_user();
if (!in_array('administrator', $user->roles)) die;
