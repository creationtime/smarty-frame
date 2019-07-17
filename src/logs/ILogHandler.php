<?php
namespace Sf\Logs;
/**
 * 定义日志接口
 */

interface ILogHandler
{
    public function write($msg);

}