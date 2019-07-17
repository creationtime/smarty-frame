<?php
namespace Sf\Cache;

/**
 * CacheInterface
 */
interface CacheInterface
{
    /**
     * 构建真正的 key，避免特殊字符影响实现
     */
    public static function buildKey($key);

    /**
     * 根据 key 获取缓存的值
     */
    public static function get($key);

    /**
     * 判断 key 是否存在
     */
    public static function exists($key);

    /**
     * 根据 keys 数组获取多个缓存值
     */
    public static function mget($keys);

    /**
     * 根据 key 设置缓存的值
     */
    public static function set($key, $value, $duration = 0);

    /**
     * 根据数组设置多个缓存值
     */
    public static function mset($items, $duration = 0);

    /**
     * 如果 key 不存在就设置缓存值，否则返回false
     * 如果缓存已经包含密钥，则不会执行任何操作。
     */
    public static function add($key, $value, $duration = 0);

    /**
     * 根据数组，判断相应的 key 不存在就设置缓存值
     * 如果缓存已经包含这样一个键，那么现有的值和过期时间将被保留。
     */
    public static function madd($items, $duration = 0);

    /**
     * 根据 key 删除一个缓存
     */
    public static function delete($key);

    /**
     * 删除所有的缓存
     */
    public static function flush();

    /**
     * 从缓存中获取key对应的值的长度
     */
    public static function lLen($key);

    /**
     * 在key对用的list的尾部添加字符串元素
     */
    public static function rPush($key,$value);

    /**
     * 从队列最左侧取出一个值
     */
    public static function lPop($key);

    /**
     * 返回指定区间内的元素，下标从0开始
     */
    public static function lrange($key,$v1,$v2);

    /**
     * 从缓存中获取key对应的值的长度
     */
    public static function close();
}