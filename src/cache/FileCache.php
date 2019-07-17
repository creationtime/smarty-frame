<?php
namespace Sf\Cache;
use Sf\Base\Component;
use Sf\Base\Error;

/**
 * CacheInterface
 * 主要思想就是，每一个 key 都对应一个文件，缓存的内容序列化一下，存入到文件中，取出时再反序列化一下。
 * 剩下的基本都是相应的文件操作了。
 * 注意，这里的$cachePath是由Sf.php配置得来，就是将fileCache的路径转化而来
 */
class FileCache extends Component
{
    /**
     * @var string 目录缓存文件to the store。
     * 缓存文件的地址，例如/Users/jun/projects/www/simple-framework/runtime/cache/
     */
    public $cachePath;

    /**
     * 从给定的键生成规范化的缓存键。
     */
    public function buildKey($key)
    {
        if (!is_string($key)) {
            // 不是字符串就json_encode一把，转成字符串，也可以用其他方法
            $key = json_encode($key);
        }
        return md5($key);
    }

    /**
     * 使用指定的键从缓存中检索值。
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // filemtime用来获取文件的修改时间
        if (@filemtime($cacheFile) > time()) {
            // file_get_contents用来获取文件内容，unserialize用来反序列化文件内容
            return unserialize(@file_get_contents($cacheFile));
        } else {
            return false;
        }
    }

    /**
     * 检查缓存中是否存在指定的密钥。
     */
    public function exists($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // 用修改时间标记过期时间，存入时会做相应的处理
        return @filemtime($cacheFile) > time();
    }

    /**
     * 使用指定的键从缓存中检索多个值。
     */
    public function mget($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }
        return $results;
    }

    /**
     * 将键标识的值存储到缓存中。
     */
    public function set($key, $value, $duration = 0)
    {
        if(!file_exists($this->cachePath)){
            Error::error($this->cachePath.' 缓存文件夹不存在');
        }
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // serialize用来序列化缓存内容
        $value = serialize($value);
        // file_put_contents用来将序列化之后的内容写入文件，LOCK_EX表示写入时会对文件加锁
        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($duration <= 0) {
                // 不设置过期时间，设置为一年，这是因为用文件的修改时间来做过期时间造成的
                // redis/memcache 等都不会有这个问题
                $duration = 31536000; // 1 year
            }
            // touch用来设置修改时间，过期时间为当前时间加上$duration
            return touch($cacheFile, $duration + time());
        } else {
            return false;
        }
    }

    /**
     * 多项目存储在缓存中。每个包含一项价值认定的关键。
     */
    public function mset($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->set($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * 如果缓存不包含该键，则将键标识的值存储到缓存中。
     */
    public function add($key, $value, $duration = 0)
    {
        //  key不存在，就设置缓存
        if (!$this->exists($key)) {
            return $this->set($key, $value, $duration);
        } else {
            return false;
        }
    }

    /**
     * 多项目存储在缓存中。每个包含一项价值认定的关键。
     */
    public function madd($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->add($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * 从缓存中删除具有指定密钥的值
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // unlink用来删除文件
        return unlink($cacheFile);
    }

    /**
     * 从缓存中删除所有值。
     * 如果缓存在多个应用程序之间共享，请小心执行此操作。
     * @return boolean 刷新操作是否成功。
     */
    public function flush()
    {
        // 打开cache文件所在目录
        $dir = @dir($this->cachePath);

        // 列出目录中的所有文件
        while (($file = $dir->read()) !== false) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->cachePath . $file);
            }
        }

        // 关闭目录
        $dir->close();
    }
}