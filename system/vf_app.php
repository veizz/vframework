<?php
class VF_App
{
    // 配置, array
    private $_config;
    // 路由实例
    private $_router;
    // request 实例
    private $_request;

    public function __construct($config){
        $this->config($config);
        $this->_router = new VF_Router();
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  设置配置, 或者取配置
     *
     * @Param $config @array
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function config($config = null){
        if(is_null($config)){
            return $this->_config;
        }
        else if(is_array($config)){
            if(!is_array($this->_config)){
                $this->_config = array();
            }
            $this->_config = array_merge($this->_config, $config);
            return $this->_config;
        }
        else{
            return $this->_config[$config];
        }
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  取request对象实例
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function request(){
        if(is_null($this->_request)){
            $this->_request = new VF_Request($this);
        }

        return $this->_request;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  输出一个字符串
     *
     * @Param $str
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function output($str){
        header('Content-Type:text/plain; charset=UTF-8');
        echo $str;
        return true;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  添加路由
     *
     * @Param $route @string
     * @Param $callback @callback 函数
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function add($route, $callback){
        return $this->_router->add($route, $callback);
    }


    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  执行程序
     *
     * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function run(){
        return $this->_router->run();
    }
}
