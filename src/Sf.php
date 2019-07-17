<?php
/**
 * 帮助类，提供公共框架功能。
 */
class Sf extends \Sf\Base\Component
{
    /**
     * 使用给定的配置创建新对象。
     * @param string $name 对象
     */
    public static function createObject($name)
    {
        $config = require(ROOT_PATH . "/configs/$name.php");
        // create instance
        $instance = new $config['class']();
        unset($config['class']);
        // add attributes
        foreach ($config as $key => $value) {
            $instance->$key = $value;
        }
        $instance->init();//创建第三方实例
        return $instance;
    }

    /**
     * 使用给定的配置创建新对象。
     * @param string $name 对象
     */
    public static function createObject2($name)
    {
        $config = require($name);
        // create instance
        $instance = new $config['class']();
        unset($config['class']);
        // add attributes
        foreach ($config as $key => $value) {
            $instance->$key = $value;
        }
        $instance->init();//创建第三方实例
        return $instance;
    }
}