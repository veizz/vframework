<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

define('CONTROLLER_DIR', APPPATH .'controller' . DIRECTORY_SEPARATOR);
define('MODEL_DIR', APPPATH .'model' . DIRECTORY_SEPARATOR);
define('LIB_DIR', APPPATH .'lib' . DIRECTORY_SEPARATOR);
define('ROUTER_DIR', APPPATH .'router' . DIRECTORY_SEPARATOR);
define('CONFIG_DIR', APPPATH .'config' . DIRECTORY_SEPARATOR);

require_once(CONFIG_DIR . 'error.php');
require_once(CONFIG_DIR . 'db.php');
require_once(CONFIG_DIR . 'memcached.php');
require_once(CONFIG_DIR . 'default_values.php');

date_default_timezone_set('Asia/Shanghai');

$config = array(
    'error_code' => $error_code
    ,'rdb' => $rdb
    ,'wdb' => $wdb
    ,'db' => $rdb // default database config, used to protected request parmmeters.
    ,'charset' => 'utf8'
    ,'memcached' => $mc
    ,'default_values' => $_default_values
);

