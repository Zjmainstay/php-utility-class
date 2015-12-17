<?php
use PhpUtility\ShortUrl;
/**
 * Created by PhpStorm.
 * User: zjmainstay
 * Date: 15/12/16
 * Time: 17:49
 */
class ShortUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * 测试生成短链接
     */
    public function testCreateUrl()
    {
        $url = 'http://www.9douyu.com';
        $shortUrl = ShortUrl::getShortUrl($url);
        $this->assertNotEmpty($shortUrl, $shortUrl);
        echo "\nResult:" . $shortUrl;
    }
}
