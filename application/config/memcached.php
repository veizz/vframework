<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');
$_memcached = array(
    array('memcached1.hostname', 11211),
    array('memcached2.hostname', 11211),
);

$mc = new APIMemcachedClient($_memcached);
