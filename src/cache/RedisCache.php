<?php
namespace Sf\Cache;

use Redis;
use Exception;
use Sf\Base\Error;

/**
 * php原生redis类
 */
class RedisCache implements CacheInterface
{
    /**
     * @var Redis|array redis对象或redis的配置
     */
    public static $redis;
    const PRE = 'smarty-frame';

    /**
     * redis实例化
     * @throws Exception
     */
    public static function init()
    {
        if (!self::$redis) {
            extract(self::$redis);
            $redis = new Redis();

            $redis->connect(redis_config('host'), redis_config('port'));

            if (!empty(redis_config('password'))) {
                $redis->auth(redis_config('password'));
            }
            $redis->select(redis_config('database'));
            if (!empty($options)) {
                call_user_func_array([$redis, 'setOption'], $options);
            }
            self::$redis = $redis;
            unset($redis);
        }
        if (!self::$redis instanceof Redis) {
            Error::error('Cache::redis must be either a Redis connection instance.');
        }
    }

    /**
     * 统一生成key的方法
     * @param $key
     * @param array $params
     * @return string
     */
    public static function generateKey($key, $params = [])
    {
        return self::PRE . ':' . $key . ($params ? ':' . implode(':', $params) : '');
    }

    /**
     * 从给定的键生成规范化的缓存键。
     */
    public static function buildKey($key)
    {
        self::init();
        if (!is_string($key)) {
            $key = json_encode($key);
        }
        return md5($key);
    }

    /**
     * 使用指定的键从缓存中检索值。
     */
    public static function get($key)
    {
        self::init();
        $key = self::buildKey($key);
        $value = json_decode(self::$redis->get($key));
        return $value;
    }

    /**
     * 检查缓存中是否存在指定的密钥。
     */
    public static function exists($key)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->exists($key);
    }

    /**
     * 使用指定的键从缓存中检索多个值。
     */
    public static function mget($keys)
    {
        self::init();
        for ($index = 0; $index < count($keys); $index++) {
            $keys[$index] = self::buildKey($keys[$index]);
        }

        return self::$redis->mGet($keys);
    }

    /**
     * 将键标识的值存储到缓存中。
     */
    public static function set($key, $value, $duration = 0)
    {
        self::init();
        $key = self::buildKey($key);
        if ($duration !== 0) {
            $expire = (int) $duration * 1000;
            return self::$redis->set($key, json_encode($value), $expire);
        } else {
            return self::$redis->set($key, json_encode($value));
        }
    }

    /**
     * 多项目存储在缓存中。每个包含一项价值认定的关键。
     */
    public static function mset($items, $duration = 0)
    {
        self::init();
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if (self::set($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * 如果缓存不包含该键，则将键标识的值存储到缓存中。
     */
    public static function add($key, $value, $duration = 0)
    {
        self::init();
        if (!self::exists($key)) {
            return self::set($key, $value, $duration);
        } else {
            return false;
        }
    }

    /**
     * 多项目存储在缓存中。每个包含一项价值认定的关键。
     */
    public static function madd($items, $duration = 0)
    {
        self::init();
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if (self::add($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * 从缓存中删除具有指定密钥的值
     */
    public static function delete($key)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->delete($key);
    }

    /**
     * 从缓存中获取key对应的值的长度
     */
    public static function lLen($key)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->lLen($key);
    }

    /**
     * 在key对用的list的尾部添加字符串元素
     */
    public static function rPush($key,$value)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->rPush($key,$value);
    }

    /**
     * 在key对用的list的头部添加字符串元素
     */
    public static function lPush($key,$value)
    {
        self::init();
        $key = self::buildKey($key);
        var_dump($key);
        return self::$redis->lPush($key,json_encode($value));
    }

    /**
     * 从缓存中获取key对应的值的长度
     */
    public static function close()
    {
        self::init();
        return self::$redis->close();
    }

    /**
     * 从队列最左侧取出一个值
     */
    public static function lPop($key)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->lPop($key);
    }

    /**
     * 返回指定区间内的元素，下标从0开始
     */
    public static function lrange($key,$v1,$v2)
    {
        self::init();
        $key = self::buildKey($key);
        return self::$redis->lrange($key,$v1,$v2);
    }

    /**
     * 从缓存中删除所有值。
     */
    public static function flush()
    {
        self::init();
        return self::$redis->flushDb();
    }
}