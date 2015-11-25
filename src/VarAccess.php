<?php
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2015
 * 从数组中取值，如果没有，则给默认值
 */
class VarAccess {
    /**
     * 从数组中获取键值，支持特殊字符分隔的多维数组，默认键值分隔符为英文逗号
     * @param $arr 取值数组
     * @param $key 键值（默认逗号分隔多维数组键值）
     * @param null $default 默认值
     * @param string $splitKey 多维数组分隔符，默认为英文逗号
     * @return mixed
     * 
     * @usage
     * $var = VarAccess::get($arr, 'key', $defaultValue);
     * $var = VarAccess::get($arr, 'key1,key2', $defaultValue);
     * $var = VarAccess::get($arr, 'key1.key2', $defaultValue, '.');
     */
    public static function get($arr, $key, $default = null, $splitKey = ',') {
        if(!is_array($arr)) {
            return $default;
        }

        //一维数组，存在则直接返回
        if(array_key_exists($key, $arr)) {
            return $arr[$key];
        }

        //分隔符分隔多维数组
        if ( strpos($key, $splitKey) !== false ) {
            $parts = explode($splitKey, $key);
            $currentArr = $arr;
            //遍历多维数组键值
            foreach ( $parts as $partKey ) {
                //上层结果不是数组 或 上层数组不存在当前键值，取值失败
                if(!is_array($currentArr) || !array_key_exists($partKey, $currentArr)) {
                    return $default;
                }
                //每次命中的结果存入$currentArr
                $currentArr = $currentArr[$partKey];
            }

            //最终结果
            return $currentArr;
        }

        return $default;
    }
}
