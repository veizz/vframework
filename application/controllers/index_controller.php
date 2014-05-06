<?php if ( ! defined('APPPATH')) exit('No direct script access allowed');

class IndexController extends VF_Controller{

    public function __construct($app){
        parent::__construct($app);
    }

    public function index(){
        $params = $this->app->request()->get();
        echo 'this is index page<br />this is get parameters:<br /><br />';
        print_r($params);
        die;
    }

    public function meta(){
        $mm = new MetaModel($this->app);
        echo $mm->get();die;
    }

};
