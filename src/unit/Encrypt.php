<?php
namespace Sf\Unit;

use Sf\Base\Error;

/**
 * 加密工具类
 * Class Amount
 * @package common\unit
 */
class Encrypt{

    /**
     * 统一加密方法 AES/CBC/PKCS5Padding
     * @param string $input
     * @param string $key
     * @param string $iv
     * @return string
     */
    public static function encrypt(string $input, string $key, string $iv = "1234567812345678")
    {
        $my_method = 'aes-128-cbc';
        $encrypted = openssl_encrypt($input, $my_method, $key, OPENSSL_RAW_DATA, $iv);
        $data = base64_encode($encrypted);
        unset($encrypted);
        return $data;
    }

    /**
     * 统一解密函数
     * @param string $encrypt_Str
     * @param string $sKey
     * @param string $iv    须是16w位
     * @return string
     */
    public static function decrypt(string $encrypt_Str, string $sKey, string $iv = "1234567812345678")
    {
        $my_method = 'aes-128-cbc';
        $decrypted = openssl_decrypt(base64_decode($encrypt_Str), $my_method, $sKey, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }

    /**
     * 统一签名规则
     * @param array $params
     * @param string $secretKey
     * @return string
     * @throws \Exception
     */
    private static function signRule(array $params, string $secretKey){
        if(empty($params)){
            Error::error('signRule params is empty');
        }
        if(empty($secretKey)){
            Error::error('signRule secretKey is empty');
        }
        ksort($params);
        $arr = array_merge([
            'secretKey' => $secretKey,
        ], $params);
        $sign = md5(json_encode(http_build_query($arr)));
        unset($arr);
        return $sign;
    }

    /**
     * 统一验签方法
     * @param string $sign  此签名为参数$params，通过函数signRule规则获取而来
     * @param array $params
     * @param string $secretKey
     * @return bool
     * @throws \Exception
     */
    public static function sign(string $sign, array $params, string $secretKey)
    {
        if(empty($params)){
            Error::error('sign params is empty');
        }

        if(!isset($params['timestamp']) || !$params['timestamp']){
            Error::error('发送的数据参数不合法');
        }

        if(empty($secretKey)){
            Error::error('sign secretKey is empty');
        }

        // 验证请求， 10分钟失效
        if ((time() - $params['timestamp']) > 600) {
            Error::error('验证失效， 请重新发送请求');
        }

        $_sign = self::signRule($params,$secretKey);
        if ($_sign != $sign) {
            return false;
        }
        return true;
    }

    /**
     * 加签
     * @param array $params
     * @param string $secretKey
     * @return string
     * @throws \Exception
     */
    public static function addSign(array $params, string $secretKey){
        if(empty($params)){
            Error::error('addSign params is empty');
        }
        if(empty($secretKey)){
            Error::error('addSign secretKey is empty');
        }
        return self::signRule($params,$secretKey);
    }

    public static function PaddingPKCS7($input) {
        $srcdata = $input;
        $block_size = mcrypt_get_block_size ( 'tripledes', 'ecb' );
        $padding_char = $block_size - (strlen ( $input ) % $block_size);
        $srcdata .= str_repeat ( chr ( $padding_char ), $padding_char );
        return $srcdata;
    }

    public static function encryptOld($string, $key) {
        $string = static::PaddingPKCS7 ( $string );

        $cipher_alg = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv ( mcrypt_get_iv_size ( $cipher_alg, MCRYPT_MODE_ECB ), MCRYPT_RAND );

        $encrypted_string = mcrypt_encrypt ( $cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv );
        $des3 = bin2hex ( $encrypted_string );

        return $des3;
    }

    /**
     * 微信rsa加签
     * @param array $params
     * @param string $secretKey
     * @return string
     * @throws \Exception
     */
    public static function wxAddSign(array $params, string $secretKey){
        if(empty($params)){
            Error::error('addSign params is empty');
        }
        if(empty($secretKey)){
            Error::error('addSign secretKey is empty');
        }
        return self::wxSignRule($params,$secretKey);
    }

    /**
     * 微信rsa统一签名规则
     * @param array $params
     * @param string $secretKey
     * @return string
     * @throws \Exception
     */
    private static function wxSignRule(array $params, string $secretKey){
        if(empty($params)){
            Error::error('signRule params is empty');
        }
        if(empty($secretKey)){
            Error::error('signRule secretKey is empty');
        }
        krsort($params);
        $json_data = json_encode($params).$secretKey;
        $res = openssl_pkey_get_private(file_get_contents(ROOT_PATH.'/src/unit/rsaKey/1024/rsa_private_key.pem'));
        openssl_sign($json_data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 微信rsa统一验签方法
     * @param string $sign  此签名为参数$params，通过函数signRule规则获取而来
     * @param array $params
     * @param string $secretKey
     * @return bool
     * @throws \Exception
     */
    public static function wxSign(string $sign, array $params, string $secretKey)
    {
        if(empty($params)){
            Error::error('sign params is empty');
        }

        if(!isset($params['timestamp']) || !$params['timestamp']){
            Error::error('发送的数据参数不合法');
        }

        if(empty($secretKey)){
            Error::error('sign secretKey is empty');
        }

        // 验证请求， 10分钟失效
        if ((time() - $params['timestamp']) > 600) {
            Error::error('验证失效， 请重新发送请求');
        }

        $_sign = self::wxSignRule($params,$secretKey);
        if ($_sign != $sign) {
            return false;
        }
        return true;
    }
}