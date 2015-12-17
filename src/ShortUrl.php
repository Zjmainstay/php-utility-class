<?php
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2015
 * 获取缩短地址
 */

namespace PhpUtility;

class ShortUrl {
    /**
     * 获取链接缩短地址
     * @param $url  需要缩短的长地址
     */
    public static function getShortUrl($url, $type = 'Baidu') {
        $method = "getShortUrlBy{$type}";
        return call_user_func(array(__NAMESPACE__ . '\ShortUrl', $method), $url);
    }

    /**
     * 百度短短链接 http://dwz.cn
     * @param $url
     * @return mixed
     */
    protected static function getShortUrlByBaidu($url) {
        $url         = urlencode($url);
        $post       = "url={$url}&alias=&access_type=web";
        $url        = 'http://dwz.cn/create.php';
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //返回数据不直接输出
        curl_setopt($ch, CURLOPT_POST, 1);            //发送POST类型数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  //POST数据，$post可以是数组，也可以是拼接
        $content = curl_exec($ch);                    //执行并存储结果
        curl_close($ch);
        $result = json_decode($content, true);
        $shortUrl = $result['tinyurl'];

        return $shortUrl;
    }
}
