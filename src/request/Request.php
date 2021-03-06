<?php
namespace Sf\Request;

class Request {
    /**
     * 获取提交方式
     * @return string
     */
    public static function getMethod() {
        return static::getEngine()->getMethod();
    }

    /**
     * 获取请求的方法
     * @return string
     */
    public static function getAction() {
        return static::getEngine()->getAction();
    }

    /**
     * 获取请求的控制器
     * @return string
     */
    public static function getController() {
        return static::getEngine()->getController();
    }

    /**
     * 获取请求的模块
     * @return string
     */
    public static function getModule() {
        return static::getEngine()->getModule();
    }

    /**
     * 获取url中提交的方法路径
     * @return string
     */
    public static function getPath() {
        return static::getEngine()->getPath();
    }

    /**
     * 获取域名
     * @return string
     */
    public static function getDomain() {
        return static::getEngine()->getDomain();
    }

    /**
     * @param string $name
     * @param string $default
     * @return string
     */
    public static function getHeader($name, $default = null) {
        return static::getEngine()->getHeader($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasHeader($name) {
        return static::getEngine()->hasHeader($name);
    }

    /**
     * @return string[]
     */
    public static function getHeaders() {
        return static::getEngine()->getHeaders();
    }

    /**
     * @return resource
     */
    public static function openInputStream() {
        return static::getEngine()->openInputStream();
    }

    /**
     * @return string
     */
    public static function getBody() {
        return static::getEngine()->getBody();
    }

    /**
     * 获取get提交的参数
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getQueryParam($name, $default = null) {
        return static::getEngine()->getQueryParam($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasQueryParam($name) {
        return static::getEngine()->hasQueryParam($name);
    }

    /**
     * 获取提交的参数
     * @return array
     */
    public static function getQueryParams() {
        return static::getEngine()->getQueryParams();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getBodyParam($name, $default = null) {
        return static::getEngine()->getBodyParam($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasBodyParam($name) {
        return static::getEngine()->hasBodyParam($name);
    }

    /**
     * @return array
     */
    public static function getBodyParams() {
        return static::getEngine()->getBodyParams();
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public static function getCookieParam($name, $default = null) {
        return static::getEngine()->getCookieParam($name, $default);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function hasCookieParam($name) {
        return static::getEngine()->hasCookieParam($name);
    }

    /**
     * @return array
     */
    public static function getCookieParams() {
        return static::getEngine()->getCookieParams();
    }

    /**
     * @return RequestEngine
     */
    public static function getEngine() {
        $class = RequestEngine::class;
        return new $class;
    }
}
