<?php
namespace Sf\Base;

/**
 * Class Session
 * @package Sf\Base
 */
class Session{

    static function set ($name,$value){
        $_SESSION[$name] = $value;
    }

    static function get ($name){
        return $_SESSION[$name];
    }
}