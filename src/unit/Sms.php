<?php
namespace Sf\Unit;

/**
 * Interface SmsInterface
 * @package Sf\Unit
 */
interface SmsInterface{
    function sendVerifyCode($params);
    function sendDeliveryNotify($params);
}

/**
 * Class Sms
 * @package Sf\Unit
 */
class Sms {

    protected $_error = array();

    /**
     * 单例
     * @param $hid
     * @return mixed
     */
    public static function getInstance($hid) {
        static $_instance = [];
        if(!isset($_instance[$hid])){
            //$obj	=	new Sms();
            $_instance[$hid]	=	self::factory($hid);
        }
        return $_instance[$hid];
    }

    /**
     * 工厂方法，根据不同配置，调用不同短信发送类
     * @param $hid
     * @return bool
     */
    public static function factory($hid){

        $provider_name = hospital_config('SMS_PROVIDER_NAME',$hid);
        $class = "\\Sf\\Unit\\Sms\\". $provider_name;

        // 检查驱动类
        if(!empty($provider_name) && class_exists($class)) {
            $sms = new $class();
        }else {
            return false;
        }

        return $sms;
    }

    protected function set_error($error){
        $this->_error[] = $error;
    }

    public function get_error(){
        return $this->_error;
    }

}