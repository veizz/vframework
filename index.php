<?php
/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
session_start();
define('ENVIRONMENT', 'development');



if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL);
        break;
    
        case 'testing':
        case 'production':
            error_reporting(0);
        break;

        default:
            exit('The application environment is not set correctly.');
    }
}

$system_path = 'system';


$application_folder = 'application';

define('APPPATH' , dirname(__FILE__) . DIRECTORY_SEPARATOR . $application_folder . DIRECTORY_SEPARATOR);
define('SYSPATH' , dirname(__FILE__) . DIRECTORY_SEPARATOR . $system_path . DIRECTORY_SEPARATOR);

foreach(glob(SYSPATH . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "*.php") as $file){
    require_once($file);
}
require(APPPATH . 'config' . DIRECTORY_SEPARATOR . 'config.php');
foreach(glob(SYSPATH . DIRECTORY_SEPARATOR . "*.php") as $file){
    @require_once($file);
}

$app = new VF_App($config);

$dirs = array('hooks', 'models', 'controllers', 'routers');
foreach($dirs as $ac){
    foreach(glob(SYSPATH . DIRECTORY_SEPARATOR . $ac . DIRECTORY_SEPARATOR . "*.php") as $file){
        require_once($file);
    }
}

foreach($dirs as $ac){
    foreach(glob(APPPATH . DIRECTORY_SEPARATOR . $ac . DIRECTORY_SEPARATOR . "*.php") as $file){
        require_once($file);
    }
}

$app->run();
