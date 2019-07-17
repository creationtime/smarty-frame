<?php
namespace Sf\Base;

use Sf\Logs\Log;
use Sf\Base\Error;

use Exception;
/**
 * 框架底层基础类
 * 一个抽象类，实现了一个简单的run方法，run方法就是去执行以下handleRequest方法。
 * 定义了一个抽象方法handleRequest，等待被继承，实现。
 */
abstract class Application
{

    /**
     * 执行应用程序
     * 所有应用程序的主入口。
     */
    public function run()
    {
        try {
            return $this->handleRequest();
        } catch (Exception $e) {
            Log::ERROR($e);
            return Error::error($e);
        }
    }

    /**
     * 处理指定的请求
     */
    abstract public function handleRequest();
}