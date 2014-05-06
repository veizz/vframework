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
        $this->rdb = $this->app->config('rdb');
        $this->wdb = $this->app->config('wdb');
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

    protected function curl_post($url, $data, $flag = false){

        if($flag){
            $params_str = ''; 
            foreach($data as $param => $params_value){
                $params_str .= $param.'='.urlencode($params_value).'&';
            }   
            $params_str = rtrim($params_str, '&');
            $data = $params_str;
        }   

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);

        $result = curl_exec($ch);
        curl_close($ch);
        if(!isset($result) && empty($result)){
            $result = '';
        }   
        return $result;
    } //curl_post

    protected  function multi_curl($connomains,$killspace=TRUE, $timeout=3,$header=0,$follow=1 ){
        //用于保存结果           
        $res=array();
        //创建多curl对象       
        $mh = curl_multi_init();
        foreach ($connomains as $i => $url) {
            $conn[$url]=curl_init($url);
            curl_setopt($conn[$url], CURLOPT_TIMEOUT, $timeout);
            //不返回请求头，只要源码
            curl_setopt($conn[$url], CURLOPT_HEADER, $header);
            //必须为1
            curl_setopt($conn[$url], CURLOPT_RETURNTRANSFER,1);
            curl_setopt($conn[$url], CURLOPT_FOLLOWLOCATION, $follow);
            curl_multi_add_handle ($mh,$conn[$url]);
        }

        do {
            //当无数据时或请求暂停时，active=true
            $mrc = curl_multi_exec($mh,$active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);//当正在接受数据时

        while ($active and $mrc == CURLM_OK) {
            //当无数据时或请求暂停时，active=true
            //var_dump(curl_multi_select($mh));die;
            curl_multi_exec($mh, $active);
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        //返回结果
        foreach ($connomains as $i => $url) {
            //可用于取得一些有用的参数，可以认为是header
            $cinfo=curl_getinfo($conn[$url]);
            if($killspace){
                $str=trim(curl_multi_getcontent($conn[$url]));
                $res[$url]=$str; 
            }else{
                $res[$url]=curl_multi_getcontent($conn[$url]);
            }                
            //关闭所有对象 
            curl_close($conn[$url]);
            //用完马上释放资源                       
            curl_multi_remove_handle($mh, $conn[$url]);
        } 
        curl_multi_close($mh);
        $mh=NULL;
        $conn=NULL;
        $connomains=NULL;
        return $res;
    }//multi_curl
}
