<?php
/**
 * 公共配置
 */
return [
    'DEBUG' => true,

    /*邮件配置*/
    'MAIL_DRIVER' => 'smtp',
    'MAIL_HOST' => 'smtp.qq.com',
    'MAIL_PORT' => '465',
    'MAIL_USERNAME' => '',
    'MAIL_PASSWORD' => '',
    'MAIL_ENCRYPTION' => 'ssl',
    'MAIL_NICKNAME' => 'hello',

    /*上传文件配置*/
    'UPLOAD_PATH' => './uploads',
//    'UPLOAD_MAX_SIZE' => 2000000,
//    'UPLOAD_ALLOW_TYPE' => [],

    'LOG_PATH' => 'logs',

    /*网站域名*/
    'CSRF_WWW' => 'vbox-smarty.cn',
    'API_CSRF_TOKEN' => 'as@&^%#@!()da*`~121/*-+.、,|=_',
];