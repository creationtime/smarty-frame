<?php

//定义根目录路径
define('ROOT_PATH', dirname(__DIR__));//   /media/sf_Project/self/smarty-frame

/*建立单一入口，*/
require_once(__DIR__ . '/../vendor/autoload.php');

/*全局加载函数库*/
include_once ROOT_PATH . "/src/Helper.php";

require_once(ROOT_PATH . '/src/Sf.php');

$application = new Sf\Web\Application();
$application->run();

