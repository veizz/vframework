<?php
class VF_Router
{
    // http 请求的method
    protected $_method;
    // 当前url
    protected $_url;
    // 路由规则
    protected $_rules;

    public function __construct() {
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->_url = self::uri_string();
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
    public function add($route, $callback) {
        // do something
        $route = str_replace('//', '/', $route);
        $this->_rules[] = array(
            'route' => $route,
            'callback' => $callback
        );
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  调用callback 函数
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function run() {
        // compare
        $match = false;
        foreach($this->_rules as $rule) {
            if($rule['route'] == $this->_url) {
                $match = true;
                $rule['callback']();
                break;
            }
        }
        if(!$match){
            $this->run_404();
        }
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  404
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function run_404(){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
        echo '404';
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  取当前uri
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public static function uri_string(){

        if( ! isset($_SERVER['REQUEST_URI']) OR ! isset($_SERVER['SCRIPT_NAME']))
        {
            return '';
        }

        $uri = $_SERVER['REQUEST_URI'];
        if(strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
        {
            $uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
        }
        elseif(strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
        {
            $uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        if(strncmp($uri, '?/', 2) === 0)
        {
            $uri = substr($uri, 2);
        }
        $parts = preg_split('#\?#i', $uri, 2);
        $uri = $parts[0];
        if(isset($parts[1]))
        {
            $_SERVER['QUERY_STRING'] = $parts[1];
            parse_str($_SERVER['QUERY_STRING'], $_GET);
        }
        else
        {
            $_SERVER['QUERY_STRING'] = '';
            $_GET = array();
        }

        if ($uri == '/' || empty($uri))
        {
            return '/';
        }

        $uri = parse_url($uri, PHP_URL_PATH);

        // Do some final cleaning of the URI and return it
        return str_replace(array('//', '../'), '/', trim($uri, '/'));
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  生成完整的url
        *
        * @Param $uri
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public static function base_url($uri){
        if(isset($_SERVER['HTTP_HOST']))
        {
            $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $base_url .= '://'. $_SERVER['HTTP_HOST'];
            $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
        }
        else
        {
            $base_url = 'http://localhost/';
        }

        $base_url = rtrim(trim($base_url), '/') . '/';

        return $base_url . ltrim($uri, '/');
    }
}
