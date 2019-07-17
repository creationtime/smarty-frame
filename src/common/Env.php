<?php
namespace Sf\Common;

use Sf\Request\Request;

/**
 * Class Env
 * @package Sf\Common
 */
class Env
{
    public static $token;
    public static $cookie;
    public static $host;
    public static $module;
    public static $controller;
    public static $action;
    public static $sign;

    /**
     * 获取Token
     * @return string
     */
    public static function getToken()
    {
        return self::$token ?: '';
    }

    /**
     * 获取Cookie
     * @return string
     */
    public static function getCookie()
    {
        return self::$cookie ?: '';
    }

    /**
     * 获取Host
     * @return string
     */
    public static function getHost()
    {
        return Request::getDomain() ?: '';
    }

    /**
     * 获取请求的模块
     * @return string
     */
    public static function getModule()
    {
        return Request::getModule() ?: '';
    }

    /**
     * 获取请求的模块
     * @param $name
     * @return string
     * author Fox
     */
    public static function getQueryParam($name)
    {
        return Request::getQueryParam($name) ?: '';
    }

    /**
     * 获取请求的控制器
     * @return string
     */
    public static function getController()
    {
        return Request::getController() ?: '';
    }

    /**
     * 获取请求的方法
     * @return string
     */
    public static function getAction()
    {
        return Request::getAction() ?: '';
    }

    /**
     * 获取根目录
     * @return string
     */
    public static function getRootPath()
    {
        return ROOT_PATH ?: '';
    }

}
