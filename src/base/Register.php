<?php
namespace Sf\Base;

/**
 * Class Register
 */
class Register{
    protected static $objects;  //建立静态受保护的对象树

    /**
     * 将对象注册到全局的树上
     * @param $alias
     * @param $object
     */
    function set($alias,$object){
        self::$objects[$alias] = $object;
    }

    /**
     * 获取某个注册树上的对象
     * @param $name
     * @return mixed
     */
    static function get($name){
        if (!isset(self::$objects[$name])) {
            self::$objects[$name] = new $name;
        }
        return self::$objects[$name];
    }

    /**
     * 移除某个注册树上的对象
     * @param $alias
     */
    function _unset($alias){
        unset(self::$objects[$alias]);
    }
}