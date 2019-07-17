<?php
namespace Sf\Web;

use Sf\Base\Route;
/**
 * 与web相关的类
 * 所有程序都经过这里
 */
class Application extends \Sf\Base\Application
{
    /**
     * 检查路由，处理指定的请求
     * @return 响应结果
     */
    public function handleRequest()
    {
        /*设置时区 */
        date_default_timezone_set("PRC");
        error_reporting(E_ALL ^ E_STRICT ^ E_NOTICE ^ E_WARNING);// 除了E_STRICT ^ E_NOTICE ^ E_WARNING之外，报告所有的错误
        $actionInfo = Route::check($_SERVER['REQUEST_URI']);
        $controllerName = 'App\\'.$actionInfo['module'].'\\Controllers\\'.$actionInfo['controller'].'Controller';
        $controller = new $controllerName();
        unset($controllerName);

        /*回调，为当前渲染的页面赋相关值*/
        $controller->module = $actionInfo['module'];
        $controller->controller = $actionInfo['controller'];
        $controller->action = $actionInfo['action'];
        
        return call_user_func([$controller, 'on'.ucfirst(str_replace('-',' ',$actionInfo['action'])).'Action']);
    }
}