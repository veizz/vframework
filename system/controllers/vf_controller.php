<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

class VF_Controller {
    protected $app = null;
    protected $error_code = null;
    protected $request = null;

    protected $log_model = null;

    public function __construct($app){
        $this->app = $app;
        $this->error_code = $this->app->config('error_code');
        $this->res_wrap = array();
        $this->request = $this->app->request();
    }

    protected function response($errorid, $data = array(), $msg = '')
    {
        $resp = $this->res_wrap;
        $resp['meta']['err'] = $errorid;
        $resp['meta']['msg'] = $msg == '' ? $this->error_code[$errorid] : $msg;
        $resp['data'] = $data;
        return json_encode($resp);
    }


    protected function response_with_callback($errorid, $data = array(), $msg = '', $callback = ''){
        if(empty($callback)){
            return $this->error_response($errorid, $data, $msg);
        }
        if(preg_match('/[<>"\'#]+/', $callback)){
            $err_code = '30002';
            return $this->error_response($err_code, $data, 'callback function error');
        }
        $str = $this->error_response($errorid, $data, $msg);
        $str = $callback . '(' . $str . ')';
        return $str;
    }

}
