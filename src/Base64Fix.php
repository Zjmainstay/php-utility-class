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
     * for($i = 1; $i <= 3; $i++) {
     *     Base64Pad::autoFixArrayBase64Pad(['name' => str_repeat('a', $i)]);
     * }
     * for($i = 1; $i <= 3; $i++) {
     *     Base64Pad::autoFixArrayBase64Pad([str_repeat('a', $i)]);
     * }
     *
     * 效果示例：
     * jsonOrig: {"name":"a"}, len: 12
     * jsonFix : {"name":"a"}, len: 12
     * base64Orig: eyJuYW1lIjoiYSJ9
     * base64Fix : eyJuYW1lIjoiYSJ9
     * 
     * jsonOrig: {"name":"aa"}, len: 13
     * jsonFix : {"name":"aa","_pad_":""}, len: 24
     * base64Orig: eyJuYW1lIjoiYWEifQ==
     * base64Fix : eyJuYW1lIjoiYWEiLCJfcGFkXyI6IiJ9
     * 
     * jsonOrig: {"name":"aaa"}, len: 14
     * jsonFix : {"name":"aaa","_pad_":"__"}, len: 27
     * base64Orig: eyJuYW1lIjoiYWFhIn0=
     * base64Fix : eyJuYW1lIjoiYWFhIiwiX3BhZF8iOiJfXyJ9
     * 
     * jsonOrig: ["a"], len: 5
     * jsonFix : {"0":"a","_pad_":"_"}, len: 21
     * base64Orig: WyJhIl0=
     * base64Fix : eyIwIjoiYSIsIl9wYWRfIjoiXyJ9
     * 
     * jsonOrig: ["aa"], len: 6
     * jsonFix : ["aa"], len: 6
     * base64Orig: WyJhYSJd
     * base64Fix : WyJhYSJd
     * 
     * jsonOrig: ["aaa"], len: 7
     * jsonFix : {"0":"aaa","_pad_":"__"}, len: 24
     * base64Orig: WyJhYWEiXQ==
     * base64Fix : eyIwIjoiYWFhIiwiX3BhZF8iOiJfXyJ9
     */
    public static function autoFixArrayBase64Pad($arr)
    {
        $jsonOrig = $jsonStr = json_encode($arr);
        $jsonLen = strlen($jsonStr);

        if( $jsonLen % 3 != 0) {
            //加入填充数组
            $arr['_pad_'] = '';
            $jsonLen = strlen(json_encode($arr));

            //补齐缺失长度
            $autoPadLen = (3 - ($jsonLen % 3)) % 3;
            $arr['_pad_'] = str_repeat('_', $autoPadLen);
            $jsonStr = json_encode($arr);
        }

        // echo "jsonOrig: " . $jsonOrig .  ", len: " . strlen($jsonOrig) ."\njsonFix : " . $jsonStr . ", len: " . strlen($jsonStr) ."\nbase64Orig: " . base64_encode($jsonOrig) . "\nbase64Fix : " . base64_encode($jsonStr). "\n\n";

        return base64_encode($jsonStr);
    }
}
