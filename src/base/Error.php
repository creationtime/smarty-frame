<?php
namespace Sf\Base;

use Sf\Logs\Log;
use Exception;//引用原生错误类

/**
 * 框架错误类
 */
class Error extends Exception
{
    public static function error($info,$code = 500){
        if(config('DEBUG') === true){
            throw new Exception($info,$code);
        }
        exit('It\'s a wonderful thing to make mistakes, isn\'t it?');
    }
}