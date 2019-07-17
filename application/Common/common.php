<?php

/**
 * 根据指定的key，将二维数组的value转换为string，适用于mysql的in查询
 */
if(!function_exists('array_unique_join')){
    function array_unique_join($arr,$param){
        $utm_source_arr = array_unique(array_column($arr,$param));
        if(count($utm_source_arr) > 1){
            $a = implode('\',\'',$utm_source_arr);
            $utm_source = "'$a'";
        }else{
            if(empty($utm_source_arr[0])){
                $utm_source_arr[0] = '';
            }
            $utm_source = "'$utm_source_arr[0]'";
        }
        return $utm_source;
    }
}

/**
 * 根据指定的key，将一维数组的value转换为string，适用于mysql的in查询
 */
if(!function_exists('array_unique_join_one')){
    function array_unique_join_one($arr){
        $a = implode('\',\'',$arr);
        $utm_source = "'$a'";
        return $utm_source;
    }
}
