<?php
class VF_Request
{

    // 私有属性，数据库实例
    private $_db = null;

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  构造函数
     *
     * @Param $app app实例
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function __construct($app){
        // TODO: 这个地方依赖性太强了，应当想办法取消这个依赖
        $this->_db = $app->config('db');
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  取得get参数
     *
     * @Param $key @string, 可以省略
     *
     * @Returns  @mixed, array 或者 string
     */
    /* ---------------------------------------------------------------*/
    public function get($key = null){
        return $this->_get_params('get', $key);
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  取得post参数
     *
     * @Param $key @string, 可以省略
     *
     * @Returns  @mixed, array 或者 string
     */
    /* ---------------------------------------------------------------*/
    public function post($key = null){
        return $this->_get_params('post', $key);
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  取参数
     *
     * @Param $method @string, get或者post
     * @Param $key @string, 可省略
     *
     * @Returns  @mixed, array 或者 string
     */
    /* ---------------------------------------------------------------*/
    private function _get_params($method = 'get', $key = null){
        if($method == 'get'){
            $res = $_GET;
        }
        else{
            $res = $_POST;
        }

        if(is_null($key)){
            foreach($res as $key => &$value){
                $value = $this->_escape_string($value);
            }
            unset($value);
        }
        else{
            $res = $res[$key];
            $res = $this->_escape_string($res);
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  转义字符，防sql注入
     *
     * @Param $str @string 未做过滤的输入参数
     *
     * @Returns  @string, 处理过的输入参数
     */
    /* ---------------------------------------------------------------*/
    private function _escape_string($str){
        $str = $this->_db->real_escape_string($str);
        return $str;
    }

    /** 
     * 从请求中得到参数，并且是安全的参数
     * 
     * @param keys array('key' => 'default val', 'key2' => 'default 2', ...);
     * 
     * @return array('key' => 'default or request value', 'key2' => ...);
     */
    public function get_param_from_all($keys){   
        $params = $this->is_get() ? $this->get() : $this->post();
        foreach($keys as $key => $val){
            if(isset($params[$key])){
                //$keys[$key] = $this->_db->real_escape_string($params[$key]);
                $keys[$key] = $params[$key];
            }
        }   
        return $keys;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  查看当前请求是GET方法还是POST方法
     *
     * @Returns  @bool
     */
    /* ---------------------------------------------------------------*/
    public function is_get(){
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
}
