<?php
class APIMemcachedClient
{
    private $mc_arr;
    private $life_time;
    private $server_arr;
    //private $persistent_id;

    //public function __construct($server_arr, $persistent_id = 'default'){


    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  constructor
        *
        * @Param $server_arr
        *   array(
        *       array('127.0.0.1', 11211),
        *       array('127.0.0.1', 11212),
        *   )
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    // do NOT support persistent_id any more, there are some bugs
    //public function __construct($server_arr, $persistent_id = 'default'){
    public function __construct($server_arr){
        //TODO:set connetion pool size
        //TODO:set expiration
        $this->server_arr = $server_arr;
        $this->mc_arr = array();
        //$this->persistent_id = $persistent_id;

        $i = 0;
        foreach($this->server_arr as $server){
            //$mc = new Memcached($this->persistent_id .'_'. $i);
            $mc = new Memcached();
            //if you use this, it is just one connection, 
            //and things are just put in one of the servers
            //$mc = new Memcached($this->persistent_id);
            $mc->addServer($server[0], $server[1]);
            $this->mc_arr[] = $mc;
            $i++;
        }
    }

    /*
    public function add_server($host, $port){
        //add server to server array
        //sync keys and values
        //TODO:set persistent_id ++
        $mc = new Memcached($this->persistent_id);
        $mc->addServer($host, $port);
        $this->mc_arr[] = $mc;
    }
     */

    /*
    public function remove_server(){
    }
     */

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  添加到memcached
     *
     * @Param $key 
     * @Param $value string or array, (some thing can be string)
     *
     * @Returns true;
     */
    /* ---------------------------------------------------------------*/
    public function add($key, $value, $expiration = -1){
        if($expiration < 0){
            //set expiration to one min
            $expiration = 60;
        }
        $res = false;
        foreach($this->mc_arr as $mc){
            $_res = $mc->add($key, $value, $expiration);
            if($_res){
                $res = true;
            }
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  set
     *
     * @Param $key
     * @Param $value string or array, (some thing can be string)
     * @Param $expiration 过期时间
     *
     * @Returns  true
     */
    /* ---------------------------------------------------------------*/
    public function set($key, $value, $expiration = -1){
        //$i = 1;
        if($expiration < 0){
            //set expiration to one min
            $expiration = 60;
        }
        $res = false;
        foreach($this->mc_arr as $mc){
            $_res = $mc->set($key, $value, $expiration);
            if($_res){
                $res = true;
            }
            //$mc->set($key, $value.'_'.$i);
            //++$i;
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  删除多个memcached中的key——value对
     *
     * @Param $key
     *
     * @Returns  存在未被删除的节点，返回false, 全部删除返回true
     */
    /* ---------------------------------------------------------------*/
    public function delete($key){
        $res = true;
        foreach($this->mc_arr as $mc){
            $_res = $mc->delete($key);
            if(!$_res){
                $res = false;
            }
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  
     *
     * @Param $key
     * @Param $value
     *
     * @Returns 有一个未被替换，返回false, 全部被替换，返回true
     */
    /* ---------------------------------------------------------------*/
    public function replace($key, $value){
        $res = true;
        foreach($this->mc_arr as $mc){
            $_res = $mc->replace($key, $value);
            if(!$_res){
                $res = false;
            }
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
     * @Synopsis  
     *
     * @Param $key
     *
     * @Returns  如果memcached中有值，返回值，如果没有，返回false
     */
    /* ---------------------------------------------------------------*/
    public function get($key){
        foreach($this->mc_arr as $mc){
            $res = $mc->get($key);
            if($res){
                return $res;
            }
        }
        return false;
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  多个memcached中的值做加操作, 不安全，可能有不同步
        *
        * @Param $key 
        * @Param $offset
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function increment($key, $offset = 1){
        // 可能会引起不同步
        $res = false;
        foreach($this->mc_arr as $mc){
            $res = $mc->increment($key, $offset);
        }
        return $res;
    }

    /* ---------------------------------------------------------------*/
    /**
        * @Synopsis  多个memcached中的值做减操作, 不安全，可能有不同步
        *
        * @Param $key 
        * @Param $offset
        *
        * @Returns  
     */
    /* ---------------------------------------------------------------*/
    public function decrement($key, $offset = 1){
        // 可能会引起不同步
        $res = false;
        foreach($this->mc_arr as $mc){
            $res = $mc->decrement($key, $offset);
        }
        return $res;
    }

    public function flush(){
        return true;
        //hmm, do nothing...
        //ACTION!!!
    }
}

//$memcache_servers = array(
//    array('localhost', 11211)
//    ,array('localhost', 11212)
//);
//
//$mc = new APIMemcachedClient($memcache_servers);
//
//$key = 'test_key';
//$value = 'test_value';
//
//$success = $mc->set($key, $value);
//$val = $mc->get($key);
//
//var_dump($val);

