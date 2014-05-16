<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

class VCurl {
    public function request($url, $data = null, $flag = false, $post = true){
        if($data)
            $data = http_build_query($data);

        $ch = curl_init();
        if($flag){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: multipart/form-data',
            ));
        }   
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
        if($post) {
            curl_setopt($ch, CURLOPT_POST, 1); 
            if($data)
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        curl_close($ch);
        if(!isset($result) && empty($result)){
            $result = '';
        }   
        return $result;
    } //request

    public function multi_request($connomains,$killspace=TRUE, $timeout=3,$header=0,$follow=1 ){
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
