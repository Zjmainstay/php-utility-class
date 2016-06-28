# php-utility-class
收集PHP工具类

##安装

1. git clone https://github.com/Zjmainstay/php-utility-class.git vendor/php-utility-class

2. add autoload into composer.json

    ```
    "autoload": {
          "psr-4": {
              "PhpUtility\\": "vendor/php-utility-class/src"
          }
    }
    ```

3. composer dump-autoload
4. use class like `$imageWatermark = new PhpUtility\ImageWaterMark;`

##VarAccess
从数组中获取键值，支持特殊字符分隔的多维数组，默认键值分隔符为英文逗号，不存在则返回默认值。
可以更智能地处理数组取值，免去isset()判断及可能产生的Notice错误。

##ShortUrl
利用百度短网址 http://dwz.cn 生成短地址工具类

##ImageWatermark
图像添加水印工具类，支持水印位置、水印透明度设置

