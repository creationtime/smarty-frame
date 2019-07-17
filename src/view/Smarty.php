<?php
namespace Sf\View;

/**
 * 引入smarty模板
 */
class Smarty extends \Smarty{

    public function __construct()
    {
        parent::__construct();
        $this->setConfig();
    }

    /**
     * 重新定义smarty属性，让其直接找到对应静态文件
     */
    private function setConfig(){
        //设置模板目录（静态页面）
        $this->template_dir = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/Views/';
        //设置编译目录,
        $this->compile_dir = ROOT_PATH.'/application/'.$GLOBALS['route']['module'].'/Runtime';

        if (config('DEBUG')) {
            $this->caching = false;
            $this->cache_lifetime = 0;
        } else {
            //缓存文件目录 我们根据debug把缓存关了，所以可以无视。
            $this->cache_dir = './smarty/cache/';
            $this->caching = true;
            $this->cache_lifetime = 120;
        }
    }

}
