<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

$_readdb_config = array(
    'host' => '127.0.0.1'
    , 'port' => 3306
    , 'user' => 'root'
    , 'password' => '123456'
    , 'database' => 'test_db'
    , 'charset' => 'utf8'
    , 'persistent' => true
);

$_writedb_config = array(
    'host' => '127.0.0.1'
    , 'port' => 3306
    , 'user' => 'root'
    , 'password' => '123456'
    , 'database' => 'test_db'
    , 'charset' => 'utf8'
    , 'persistent' => true
);

$rdb = new Db_Mysql($_readdb_config);
$wdb = new Db_Mysql($_writedb_config);
