<?php
// ACTION!!!: check before use, may be some bugs
class MemcacheSessionHandler implements SessionHandlerInterface
{
    //TODO:if memcache can't  connect, switch to file handler
    private $mc;
    private $life_time;
    private $_mc_host;
    private $sess_module;
    private $savePath;

    public function __construct($host_port_arr){
        $this->_mc_host = $host_port_arr;
        //session.gc_maxlifetime=1440
        $this->life_time = 1440;
        $this->savePath = '/tmp/session';
        $this->sess_module = 'memcache';
        if (class_exists('Memcache')) {  
            $this->sess_module = 'memcache';
        }  
        else if (class_exists('Memcached')) {
            $this->sess_module = 'memcached';
        }
        else{
            $this->sess_module = 'file';
        }

        //parent::__construct();
    }   

    public function open($savePath, $sessionName){
        return call_user_func(array($this, $this->sess_module."_open"),  $savePath, $sessionName);
    }

    public function close(){
        return call_user_func(array($this, $this->sess_module."_close"));
    }
    public function read($id){
        return call_user_func(array($this, $this->sess_module."_read"),  $id);
    }
    public function write($id, $data){
        return call_user_func(array($this, $this->sess_module."_write"),  $id, $data);
    }
    public function destroy($id){
        return call_user_func(array($this, $this->sess_module."_destroy"),  $id);
    }
    public function gc($maxlifetime){
        return call_user_func(array($this, $this->sess_module."_gc"),  $maxlifetime);
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcache_open($savePath, $sessionName)
    {   
        $this->mc = new Memcache();
        $valid_servers = array();
        //validate
        foreach($this->_mc_host as $mchost){
            if($this->mc->connect($mchost['host'], $mchost['port'])){
                $valid_servers[] = array('host' => $mchost['host'], 'port' => $mchost['port']);
            }   
        }   
        $this->mc->close();
        //add servers
        if(count($valid_servers) == 0){ 
            return false;
        }   
        foreach($valid_servers as $mchost){
            $this->mc->addServer($mchost['host'], $mchost['port']);
        }   
        return true;
    }   

    protected function memcache_close()
    {
        return true;
    }

    //Returns an encoded string of the read data. 
    //If nothing was read, it must return an empty string. 
    //Note this value is returned internally to PHP for processing.
    protected function memcache_read($id)
    {
        $sess = $this->mc->get("sess_$id");
        return (string)$sess;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    //$id, $data should be string
    protected function memcache_write($id, $data)
    {
        $r = $this->mc->set("sess_$id", $data, false, $this->life_time);
        return $r;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcache_destroy($id)
    {
        $r = $this->mc->delete("sess_$id");
        return $r;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcache_gc($maxlifetime)
    {
        //just set maxlifetime, memcache will delete the values while expire
        $this->life_time = $maxlifetime;
        return true;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcached_open($savePath, $sessionName)
    {   
        $this->mc = new Memcached();
        $valid_servers = array();
        //validate
        foreach($this->_mc_host as $mchost){
            $this->mc->addServer($mchost['host'], $mchost['port']);
        }
        $stat = $this->mc->getStats();
        $need_reset = false;
        foreach($stat as $key => $host){
            if($host['uptime'] != 0){
                $_h = explode(":", $key);
                $valid_servers[] = array('host' => $_h[0], 'port' => $_h[1]);
            }
            else{
                $need_reset = true;
            }
        }

        if($need_reset){
            //add servers
            if(count($valid_servers) == 0){ 
                //TODO: switch to file handler
                return false;
            } 
            $this->mc->resetServerList();
            foreach($valid_servers as $mchost){
                $this->mc->addServer($mchost['host'], $mchost['port']);
            }
        }
        return true;
    }   

    protected function memcached_close()
    {
        return true;
    }

    //Returns an encoded string of the read data. 
    //If nothing was read, it must return an empty string. 
    //Note this value is returned internally to PHP for processing.
    protected function memcached_read($id)
    {
        $sess = $this->mc->get("sess_$id");
        return (string)$sess;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    //$id, $data should be string
    protected function memcached_write($id, $data)
    {
        $r = $this->mc->set("sess_$id", $data, $this->life_time);
        return $r;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcached_destroy($id)
    {
        $r = $this->mc->delete("sess_$id");
        return $r;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function memcached_gc($maxlifetime)
    {
        //just set maxlifetime, memcache will delete the values while expire
        $this->life_time = $maxlifetime;
        return true;
    }


    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function file_open($savePath , $sessionName)
    {   
        if($savePath != ''){
            $this->savePath = $savePath;
        }
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }
        return true;
    }   

    protected function file_close()
    {
        return true;
    }

    //Returns an encoded string of the read data. 
    //If nothing was read, it must return an empty string. 
    //Note this value is returned internally to PHP for processing.
    protected function file_read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    //$id, $data should be string
    protected function file_write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function file_destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    //The return value of the session storage (usually 0 on success, 1 on failure).
    protected function file_gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}

//$memcache_host = array(array('host' => 'a.ku6.com', 'port' => 11211));
//$handler = new MemcacheSessionHandler($memcache_host);
//session_set_save_handler($handler, true);
//session_start();
//$_SESSION['test_s'] = 'this is test session';
//var_dump($_SESSION);
?>
