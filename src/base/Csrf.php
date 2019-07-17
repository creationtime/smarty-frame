<?php
namespace Sf\Base;

class Csrf{

    static function checkApiCsrfToken($name){
        $getHeaders = getHeaders($name);
        if($getHeaders !== self::getApiCsrfToken()){
            return false;
        }
        return true;
    }

    static function getApiCsrfToken(){
        $token = Config('API_CSRF_TOKEN');
        return md5(md5($token));
    }
}