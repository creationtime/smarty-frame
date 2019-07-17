<?php
namespace Sf\Logs;
/**
 * 处理日志文件
 */

class CLogFileHandler implements ILogHandler
{
    private $handle = null;

    public function __construct($file = '')
    {
        if(!file_exists($file)){
            mkdir (substr($file,0,strripos($file,'/')),0777,true);
        }
        $this->handle = fopen($file,'a');
    }

    public function write($msg)
    {
        fwrite($this->handle, $msg, 4096);
    }

    public function __destruct()
    {
        fclose($this->handle);
    }
}