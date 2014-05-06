<?php
/**
 * @file mysql.lib.php
 * @Synopsis  mysql connection lib, read/write separate, load balancing
 * @author veizz: veizzsmile@gmail.com
 * @version 0.0.1
 * @date 2013-07-31
 */

class Db_Mysql_Lib
{
    private $_config;
    private $_rw;
    private $_ro;
    public function __construct($config){
        $default_rw = array(
            'host' => '127.0.0.1'
            ,'port' => 3306
        );
        $default_ro = array(
            array( 'host' => '127.0.0.1' ,'port' => 3306)
        );
        $default_config = array(
            'user' => 'root'
            ,'password' => ''
            ,'database' => 'test'
            ,'charset' => 'utf8'
            ,'persistent' => true
        );

        if(isset($config['rw'])){
            foreach($default_rw as $key => $value){
                if(isset($config['rw'][$key])){
                    $default_rw[$key] = $config['rw'][$key];
                }
            }
        }

        if(isset($config['ro']) 
            && is_array($config['ro']) 
            && count($config['ro']) > 0
        ){
            $default_ro = $config['ro'];
        }

        if(isset($config['config'])){
            foreach($default_config as $key => $value){
                if(isset($config['config'][$key])){
                    $default_config[$key] = $config['config'][$key];
                }
            }
        }


        $this->_config = array(
            'rw' => $default_rw
            ,'ro' => $default_ro
            ,'config' => $default_config
        );

        //require_once('Mysql.class.php');

    }

    public function get_rw(){
        if(!isset($this->_rw)){
            $config = array(
                'host' => $this->_config['rw']['host']
                ,'port' => $this->_config['rw']['port']
                ,'user' => $this->_config['config']['user']
                ,'password' => $this->_config['config']['password']
                ,'database' => $this->_config['config']['database']
                ,'charset' => $this->_config['config']['charset']
                ,'persistent' => $this->_config['config']['persistent']
            );
            $this->_rw = new Db_Mysql($config);
        }
        return $this->_rw;
    }

    public function get_ro(){
        if(!isset($this->_ro)){
            $length = count($this->_config['ro']);
            if($length <= 0){
                //error
                return null;
            }
            $index = rand(0, $length-1);
            $ro_config = $this->_config['ro'][$index];
            $config = array(
                'host' => $ro_config['host']
                ,'port' => $ro_config['port']
                ,'user' => $this->_config['config']['user']
                ,'password' => $this->_config['config']['password']
                ,'database' => $this->_config['config']['database']
                ,'charset' => $this->_config['config']['charset']
                ,'persistent' => $this->_config['config']['persistent']
            );
            $this->_ro = new Db_Mysql($config);
        }
        return $this->_ro;
    }
}
