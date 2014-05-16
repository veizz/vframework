<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');
class VF_Model{
    protected $app = null;
    protected $error_code = null;
    protected $rdb = null;
    protected $wdb = null;
    protected $mc = null;

    public function __construct($app){
        $this->app = $app;
        $this->error_code = $this->app->config('error_code');
        // 更多的时候，可能用不上读写分离,因此我们这里最好能检测一下
        $this->rdb = $this->app->config('rdb');
        $this->wdb = $this->app->config('wdb');
        if(isset($this->app->config('db'))){
            $this->rdb = $this->wdb = $this->app->config('db');
        }
        $this->mc = $this->app->config('memcached');
    }

    protected function get_client_ip(){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) { 
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];    
        }   
        elseif (isset($_SERVER["HTTP_CLIENT_IP"])) { 
            $realip = $_SERVER["HTTP_CLIENT_IP"];    
        }   
        else { 
            $realip = $_SERVER["REMOTE_ADDR"];    
        }   
        return $realip;
    }   

    protected function curl_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    } // pure_curl_get

}
