<?php
namespace PhpUtility;
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2020
 * 自动补位去除数组base64产生的末尾等号
 */
class Base64Pad
{
    /**
     * 自动修复数组的base64末尾填充
     * @param  array $arr
     * @return string
     *
     * 效果示例：
     * jsonOrig: {"name":"a"}, len: 12
     * jsonFix : {"name":"a"}, len: 12
     * base64Orig: eyJuYW1lIjoiYSJ9
     * base64Fix : eyJuYW1lIjoiYSJ9
     * 
     * jsonOrig: {"name":"aa"}, len: 13
     * jsonFix : {"name":"aa","_pad_":"___"}, len: 27
     * base64Orig: eyJuYW1lIjoiYWEifQ==
     * base64Fix : eyJuYW1lIjoiYWEiLCJfcGFkXyI6Il9fXyJ9
     * 
     * jsonOrig: {"name":"aaa"}, len: 14
     * jsonFix : {"name":"aaa","_pad_":"__"}, len: 27
     * base64Orig: eyJuYW1lIjoiYWFhIn0=
     * base64Fix : eyJuYW1lIjoiYWFhIiwiX3BhZF8iOiJfXyJ9
     * 
     * jsonOrig: {"name":"aaaa"}, len: 15
     * jsonFix : {"name":"aaaa"}, len: 15
     * base64Orig: eyJuYW1lIjoiYWFhYSJ9
     * base64Fix : eyJuYW1lIjoiYWFhYSJ9
     */
    public static function autoFixArrayBase64Pad($arr)
    {
        $jsonOrig = $jsonStr = json_encode($arr);
        $jsonLen = strlen($jsonStr);

        if( $jsonLen % 3 != 0) {
            $autoPadLen = 3 - ($jsonLen + 11) % 3;
            $arr['_pad_'] = str_repeat('_', $autoPadLen);    //"_pad_":"" 填充字段长度为11，让 ( strlen($jsonStr) + 11 ) % 3 == 0
            $jsonStr = json_encode($arr);
        }

        // echo "jsonOrig: " . $jsonOrig .  ", len: " . strlen($jsonOrig) ."\njsonFix : " . $jsonStr . ", len: " . strlen($jsonStr) ."\nbase64Orig: " . base64_encode($jsonOrig) . "\nbase64Fix : " . base64_encode($jsonStr). "\n\n";

        return base64_encode($jsonStr);
    }
}
