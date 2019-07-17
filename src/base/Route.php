<?php
namespace Sf\Base;

use Exception;
use Sf\Request\Request;

/**
 * 路由控制实现类
 */
class Route{

    public static function index(string $url){
        try {
            return self::check($url);
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function check($url){
        $request_url = strtolower($url);//统一变成小写
        if(strpos($request_url,'api') !== false){
            /*此处根据url灵活定义应用接口目录名称*/
            $url_api_name = substr(substr($request_url,1),0,stripos(substr($request_url,1),"/"));
            $route_config = route_config($url_api_name);
            $type = $url_api_name;
            $controller = static::subRequestUrl($request_url,$type);
            /*若没有方法名，判断下参数中是否含有*/
            if(empty($controller)){
                $controller = Request::getQueryParam('api');
            }
        }elseif(strpos($request_url,'backend') !== false){
            $route_config = route_config('backend');
            $type = 'backend';
            $controller = static::subRequestUrl($request_url,$type);
        }else{
            $route_config = route_config('web');
            if(strpos($request_url,'?') !== false){
                $a = substr($request_url,strripos($request_url,"/")+1);
                $controller = substr($a,0,strrpos($a,"?"));//截取掉指定字符后面的字符串
            }elseif($request_url == '/'){
                $controller = '/';
            }else{
                $controller = substr($request_url,strripos($request_url,"/")+1);//截取掉指定字符前面的字符串;
            }
            $type = 'web';
        }

        if(isset($route_config[$controller])){
            return static::setRoute($route_config[$controller],$type);
        }else{
            Error::error('路由规则错误：'.$controller.'不存在');
        }

    }

    protected static function setRoute(string $data,$type){
        $url_arr = explode('/',$data);
        foreach($url_arr as $k => $v){
            $url_arr[$k] = ucwords($v);
        }
        $action = array_pop($url_arr);
        if(strpos($action,'-') !== false){
            $ac_arr = explode('-',$action);
            foreach($ac_arr as $k => $v){
                $ac_arr[$k] = ucwords($v);
            }
            $action = implode('',$ac_arr);
        }
        $GLOBALS['route']['url'] = ucwords($type).'/Controllers/'.$url_arr[0].'Controller';
        $GLOBALS['route']['module'] = ucwords($type);
        $GLOBALS['route']['controller'] = $url_arr[0];
        $GLOBALS['route']['action'] = $action;

        return $GLOBALS['route'];
    }

    /**
     * 截取$_SERVER['REQUEST_URI']字符串
     */
    protected static function subRequestUrl(string $request_url,$type){
        $len = strlen($type.'/');
        $url_str = substr($request_url,strripos($request_url,"$type/")+$len);//截取掉指定字符前面的字符串
        if(strpos($url_str,'?') !== false){
            $controller = substr($url_str,0,strrpos($url_str,"?"));//截取掉指定字符后面的字符串
        }else{
            $controller = $url_str;
        }
        return $controller;
    }
}