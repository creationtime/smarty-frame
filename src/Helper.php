<?php
/**
 * 框架公共函数类
 */

use Sf\Common\Common;

/**
 * 相当于var_dump();die;
 * @param_ $info 信息
 */
if(!function_exists('dd')){
    function dd($info){
        echo '<pre>';
        var_dump($info);
        echo '</pre>';
        die;
    }
}

/**
 * 防止注入攻击
 * @param string $string 目标字符串
 * @param string  返回数据
 */
if(!function_exists('fanXSS')){
    function fanXSS($string){
        // 生成配置对象
        $cfg = \HTMLPurifier_Config::createDefault();
        // 以下就是配置：
        $cfg->set('Core.Encoding', 'UTF-8');
        // 设置允许使用的HTML标签
        $cfg->set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]');
        // 设置允许出现的CSS样式属性
        $cfg->set('CSS.AllowedProperties', '');
        // 设置a标签上是否允许使用target="_blank"
        $cfg->set('HTML.TargetBlank', TRUE);
        // 使用配置生成过滤用的对象
        $obj = new \HTMLPurifier($cfg);
        // 过滤字符串
        return $obj->purify($string);

    }
}

/**
 * 获取一个或全部的头信息
 * @param $name
 * @return mixed
 */
if(!function_exists('getHeader')){
    function getHeaders($name = null)
    {
        if($name){
            if(strstr($name,'-') !== false){
                $name1 = str_replace('-', '_', strtoupper($name));
            }else{
                $name1 = strtoupper($name);
            }
            $headers = $_SERVER['HTTP_'.$name1] ?? null;
            if(empty($headers)) Error::error($name.' 头信息不存在');
        }else{
            foreach ($_SERVER as $name => $value)
            {
                if (substr($name, 0, 5) == 'HTTP_')
                {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }

        return $headers;
    }
}

/**
 * 获取configs下app配置信息
 * @param_ $name key
 */
function config($name){
    return Common::config($name);
}

/**
 * 获取configs下db配置信息
 * @param_ $name key
 */
function db_config($name){
    $res = include_once (ROOT_PATH . "/configs/db.php");
    if(is_array($res)){
        $GLOBALS['db_config'] = $res;
    }
    return $GLOBALS['db_config'][$name];
}

/**
 * 获取configs下route配置信息
 * @param_ $name key
 */
function route_config($name){
    $res = include_once (ROOT_PATH . "/configs/routes.php");
    if(is_array($res)){
        $GLOBALS['route_config'] = $res;
    }
    return $GLOBALS['route_config'][$name];
}

/**
 * 获取configs下route配置信息
 * @param_ $name key
 */
function redis_config($name){
    $res = include_once (ROOT_PATH . "/configs/redisCache.php");
    if(is_array($res)){
        $GLOBALS['redis_config'] = $res;
    }
    return $GLOBALS['redis_config'][$name];
}

/**
 * 获取configs下route配置信息
 * @param_ $name key
 */
function redis_config2(){
    $res = include_once (ROOT_PATH . "/configs/redisCache.php");
    if(is_array($res)){
        $GLOBALS['redis_config2'] = $res;
    }
    return $GLOBALS['redis_config2'];
}

/**
 * 获取configs下rabbitMQ配置信息
 * @param_ $name key
 */
function rabbitmq_config($name){
    $res = include_once (ROOT_PATH . "/configs/rabbitMQ.php");
    if(is_array($res)){
        $GLOBALS['rabbitmq_config'] = $res;
    }
    return $GLOBALS['rabbitmq_config'][$name];
}

/**
 * 公用success的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
if(!function_exists('json_success')){
    function json_success($message = "操作成功" , $data = array()){
        return Common::json_success($message , $data);
    }
}

/**
 * 公用fail的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
if(!function_exists('json_fail')){
    function json_fail($message = "操作失败" , $data = array()){
        return Common::json_fail($message,$data);
    }
}

/**
 * 统一获取get、post
 */
if(!function_exists('input')){
    function input($name){
        if(strstr($name,'get') !== false){
            return $_GET;
        }elseif(strstr($name,'post') !== false){
            return $_POST;
        }elseif(strstr($name,'params') !== false){
            return $_POST[0] ?? $_GET;
        }
        dd('input:'.$name);
    }
}

/** 
 * [生成缩略图]    
 * @param  string $service_image_path 文件最终保存路径
 * @param  string $RealPath  上传文件的临时路径 
 * @param  string $upload_file_name 上传文件的额最终名称    
 * @param  string $width 宽度    
 * @param  string $height 高度    
 * @return  string [返回处理后的文件路径]     
 */

if(!function_exists('thumbImage')){
    function thumbImage($service_image_path,$RealPath,$upload_file_name,$width,$height){
        $thumb_name = $width.'thumb'.$upload_file_name;//缩略图名

        /*缩略图保存路径及名称，这里将文件保存在了storage目录下，也可使用public_path()*/
        $file_path = storage_path($service_image_path);
        $thumbnail_file_path = $file_path.'/'.$thumb_name;

        /*添加水印*/
        //$logo = ImageManagerStatic::make(storage_path('app/public/static/shuiyin.png'))->opacity(10);   //0-100 值越小透明度越高

        $image = ImageManagerStatic::make($RealPath)->resize($width, $height)->save($thumbnail_file_path);  //生成缩略图->insert($logo, 'top-left', 10, 10)
        if ($image) {
            return $service_image_path.'/'.$thumb_name;
        }
    }
}

/**
 * 文件下载操作
 * @param string $src_file	要下载的文件
 */
function downloadFile($src_file) {
    //读取文件信息
    $path_info = pathinfo($src_file);
    //发送头编码 告知文件类型
    header('Content-type: application/' . $path_info["extension"]);
    //以附件形式 不直接打开
    header('Content-Disposition: attachment; filename=' . iconv("utf-8", "gbk", $path_info["basename"]));
    //读取保存缓冲区的文件
    readfile($src_file);
}

/*
 * 递归删除非空目录
 */
function deleteFold($dirRoot) {
    //将UTF8文件编码成GBK 用于系统验证
    $dirRoot = iconv("utf-8", "gbk", $dirRoot);
    if (!is_readable($dirRoot))		return;
    //打开目录
    $handle = opendir($dirRoot);
    //读取文件
    while ($file = readdir($handle)) {
        //过滤快捷方式等
        if ($file == "." || $file == "..")  continue ;
        //组装文件
        $full_path = $dirRoot . "/" . $file; //纯GBK
        //如果是目录 递归执行删除
        if (is_dir($full_path)) {
            deleteFold(iconv("gbk", "utf-8", $full_path));
            //如果是文件 则直接删除之
        } elseif (is_file($full_path)) {
            unlink($full_path);
        }
    }
    //关闭目录
    closedir($handle);
    //删除最外层
    rmdir($dirRoot);
}

/**
 * 引入公共函数
 */
if(!function_exists('requireCommon')){
    function requireCommon(){
        $dirs = scandir(ROOT_PATH.'/application/');
        foreach($dirs as $v){
            if(strstr($v,'.')){
                continue;
            }
            if(file_exists(ROOT_PATH.'/application/'.$v.'/'.'common.php')){
                include(ROOT_PATH.'/application/'.$v.'/'.'common.php');
            }
        }
    }
}
requireCommon();