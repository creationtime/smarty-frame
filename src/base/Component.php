<?php
namespace Sf\Base;

/**
 * 大多数SF类的基类。
 * 防止调用没有init相关方法的类报错
 */
class Component
{
    /**
     * initializes的组件。
     * 在用初始化对象后，在构造函数的末尾调用此方法。给定配置。
     */
    public function init()
    {
    }
}