<?php
namespace PhpUtility;
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2024
 * 自动修复JSON字符串内的双引号转义
 */
class JsonFix
{
    /**
     * 自动修复任意嵌套双引号异常JSON数据
     * @param  string $input
     * @return string
     *
     * 反斜杠个数推理过程
     * 0次，不需要转义
     * 1次，对双引号转义1次，是\"
     * 2次，对\"转义，是\\ + \"，所以是\\\"
     * 3次，对\\\"转义，是\\\\\\ + \"，所以是\\\\\\\"
     * 以此类推....
     * 反斜杠个数为：(2<<(Level-1)) - 1，Level >= 1
     */
    public static function autoFix($input)
    {
        //移除双引号前面的反斜杠，避免干扰修复
        $input = preg_replace('#\\\\(?=[\\\\]*")#i', '', $input);

        $stack = new \SplStack();
        $output = '';
        $level = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            // 如果当前字符是双引号
            if ($input[$i] === '"') {
                if($stack->isEmpty()) { //首个双引号
                    $stack->push(1);
                    $output .= '"';
                    $level++;
                } else {
                    //判断是不是尾部双引号
                    if(isset($input[$i+1]) && (in_array($input[$i+1], [':', ',', '}']))) { //尾部，弹出一个
                        $level--;
                        $stack->pop();
                        if($stack->isEmpty()) { //最后一个双引号
                            $output .= '"';
                        } else {
                            //按层级补充转义
                            $output .= str_repeat('\\', (2<<($level-1))-1) . '"'; //内部双引号，加转义
                        }
                    } else { //不是尾部，继续入栈
                        $stack->push(1);
                        //按层级补充转义
                        $output .= str_repeat('\\', (2<<($level-1))-1) . '"';
                        $level++;
                    }
                }
            } else {
                $output .= $input[$i];
            }
        }

        return $output;
    }
}