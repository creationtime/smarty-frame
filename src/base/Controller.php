<?php
namespace Sf\Base;

/**
 * 包含控制器逻辑的基类
 * 不需要每写一个要去渲染页面的action，都要去找相应路径的view，然后把它require进来。
 * 所以象出一个Controller的基类，实现一个渲染页面的方法，让其他的controller继承，就可以使用相应的方法。
 */
class Controller
{
    /**
     * @var string 记录当前执行的模块
     */
    public $module;
    /**
     * @var string 记录当前执行的控制器
     */
    public $controller;
    /**
     * @var action 记录当前正在执行的方法。
     */
    public $action;

    /**
     * @var smarty 记录当前smarty
     */
    public $smarty;
}