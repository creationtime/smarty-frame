<?php
namespace Sf\Logs;
/**
 * 日志类
 */
class Log
{
    private $handler = null;
    private $level = 15;

    private static $instance = null;

    /**
     * private仅可以log内部调用
     */
    private function __construct(){}

    private function __clone(){}

    /**
     * 初始化日志
     * @param int $level
     * @return null|Log
     */
    public static function Init($level = 15)
    {
        $handler = new CLogFileHandler(ROOT_PATH.'/'.config('LOG_PATH').'/'.date('Y').'/'.date('Ym').'/'.date('Ymd').'.log');
        if ( ! self::$instance instanceof self ) {
            self::$instance = new self();
            self::$instance->__setHandle($handler);
            self::$instance->__setLevel($level);
        }
        return self::$instance;
    }

    private function __setHandle($handler){
        $this->handler = $handler;
    }

    private function __setLevel($level)
    {
        $this->level = $level;
    }

    public static function DEBUG($msg,$data = null)
    {
        self::Init(1);
        self::$instance->write(1, $msg);
    }

    public static function WARN($msg,$data = null)
    {
        self::Init(4);
        self::$instance->write(4, $msg);
    }

    public static function ERROR($msg,$data = null)
    {
        $debugInfo = debug_backtrace();
        $stack = "[";
        foreach($debugInfo as $key => $val){
            if(array_key_exists("file", $val)){
                $stack .= ",file:" . $val["file"];
            }
            if(array_key_exists("line", $val)){
                $stack .= ",line:" . $val["line"];
            }
            if(array_key_exists("function", $val)){
                $stack .= ",function:" . $val["function"];
            }
        }
        $stack .= "]";
        self::Init(8);
        self::$instance->write(8, $stack.$msg,$data);
    }

    public static function INFO($msg,$data = null)
    {
        self::Init(2);
        self::$instance->write(2, $msg);
    }

    private function getLevelStr($level)
    {
        switch ($level)
        {
            case 1:
                return 'debug';
                break;
            case 2:
                return 'info';
                break;
            case 4:
                return 'warn';
                break;
            case 8:
                return 'error';
                break;
            default:

        }
    }

    protected function write($level,$msg,$data = null)
    {
        if(($level & $this->level) == $level )
        {
            $session_id = '';
            if(session_id()){
                $session_id = ' | '.session_id().' | ';
            }
            if(empty($data)){
                $data = '';
            }elseif(is_array($data)){
                $data = json_encode($data);
            }
            $msg = '['.date('Y-m-d H:i:s').']['.$this->getLevelStr($level).'] '.$session_id.$msg."\n".$data;
            $this->handler->write($msg);
        }
    }
}