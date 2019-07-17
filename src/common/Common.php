<?php
namespace Sf\Common;

use Sf\Base\Error;
use Sf\Logs\Log;
use Exception;
use Sf\Unit\Encrypt;
use Sf\Unit\Rsa;

class Common
{

    /**
     * 获取configs下app配置信息
     * @param_ $name key
     */
    public static function config($name){
        /*调用创建对象类，进行数据库连接，优先级是先子再公共*/
        $path = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/'.'config.php';
        if(!file_exists($path)){
            $path = ROOT_PATH . "/configs/config.php";
        }
        if(!file_exists($path)){
            throw new Exception('配置文件不存在');
        }
        $res = require($path);
        if(is_array($res) && $res[$name]){
            $GLOBALS['config'] = $res;
        }elseif(is_array($res) && !$res[$name]){
            $res = require(ROOT_PATH . "/configs/config.php");
            if(is_array($res)){
                $GLOBALS['config'] = $res;
            }
        }else{
//            \Sf\Base\Error::error('配置项：'.$name.'不存在');
            return null;
        }

        return $GLOBALS['config'][$name];
    }

    /**
     * 公用的方法  返回json数据，进行信息的提示
     * @param $status 状态
     * @param string $message 提示信息
     * @param array $data 返回数据
     */
    public static function json_exit($code = 200, $message = "操作成功", $data = array())
    {
        $obj = new \stdClass();
        $obj->code = $code;
        $obj->message = $message;
        if(empty($data)){
            $d = new \stdClass();
            $d->data = array('data'=>$data);
            $obj->data = $d->data;
        }else{
            $obj->data = $data;
        }

        if(config('SECRET_KEY') && config('ENCRYPT_IV')){
//            echo Rsa::privEncrypt(json_encode($data,JSON_UNESCAPED_UNICODE));//加密
            echo Encrypt::encrypt(json_encode($data,JSON_UNESCAPED_UNICODE), config('SECRET_KEY'), config('ENCRYPT_IV'));
        }else{
            echo json_encode($obj,JSON_UNESCAPED_UNICODE);
//            dd(json_last_error_msg());
        }


        exit;
    }

    /**
     * 公用success的方法  返回json数据，进行信息的提示
     * @param $status 状态
     * @param string $message 提示信息
     * @param array $data 返回数据
     */
    public static function json_success($message = "操作成功", $data = array())
    {
        return self::json_exit(200, $message, $data);
    }

    /**
     * 公用fail的方法  返回json数据，进行信息的提示
     * @param $status 状态
     * @param string $message 提示信息
     * @param array $data 返回数据
     */
    public static function json_fail($message = "操作失败", $data = array())
    {
        Log::ERROR($message, $data);
        return self::json_exit(500, $message, $data);
    }
}