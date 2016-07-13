<?php

require_once __DIR__.'/../vendor/autoload.php';

$autologin = new CurlAutoLogin();

// ***********************************************************
// 这是一个关于前置和后置处理的例子，但是用户信息不正确，无法实际运行。
// ***********************************************************

//1. 初始化后台登录页
$firstCurl = "curl 'http://www.zjmainstay.cn/administrator/index.php' -H 'Host: www.zjmainstay.cn' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:47.0) Gecko/20100101 Firefox/47.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3' -H 'Accept-Encoding: gzip, deflate' -H 'Cookie: Hm_lvt_1526d5aecf5561ef9401f7c7b7842a97=1468327822,1468327904,1468341636,1468411918; c0892bdc49046c5b1c407e9123b669e8=q2d3ttscevhlnitifk35rom8u0; Hm_lpvt_1526d5aecf5561ef9401f7c7b7842a97=1468415907; c1048fe61f426c9e18ed2a7baff5791a=obr82aganfmcidhqtphn5t8fb2' -H 'Connection: keep-alive'";
//后置处理得到csrf token
$csrfToken = $autologin->execCurl($firstCurl, false, function($parseCurlResult, $execCurlResult) {
        preg_match('#<input type="hidden" name="([^"]+)" value="1" />#is', $execCurlResult, $match);
        return $match[1];
    });

//2. 提交登录表单
$secondCurl = "curl 'http://www.zjmainstay.cn/administrator/index.php' -H 'Host: www.zjmainstay.cn' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:47.0) Gecko/20100101 Firefox/47.0' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' -H 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3' -H 'Accept-Encoding: gzip, deflate' -H 'Referer: http://www.zjmainstay.cn/administrator/?blogadmin=hzg' -H 'Cookie: Hm_lvt_1526d5aecf5561ef9401f7c7b7842a97=1468327822,1468327904,1468341636,1468411918; c0892bdc49046c5b1c407e9123b669e8=q2d3ttscevhlnitifk35rom8u0; Hm_lpvt_1526d5aecf5561ef9401f7c7b7842a97=1468414251; c1048fe61f426c9e18ed2a7baff5791a=obr82aganfmcidhqtphn5t8fb2' -H 'Connection: keep-alive' -H 'Content-Type: application/x-www-form-urlencoded' --data 'username=username&passwd=password&lang=&option=com_login&task=login&return=aW5kZXgucGhwP2Jsb2dhZG1pbj1oemc%3D&6355463aac75ade352ea6ecd5680c6fc=1'";
$realUsername = 'myusername';
$realPassword = 'mypassword';
//前置处理，替换错误的用户名、密码、csrf token
$autologin->execCurl($secondCurl, function($parseCurlResult) use ($csrfToken, $realUsername, $realPassword) {
        $parseCurlResult['post'] = str_replace('=username', "={$realUsername}", $parseCurlResult['post']);
        $parseCurlResult['post'] = str_replace('=password', "={$realPassword}", $parseCurlResult['post']);
        $parseCurlResult['post'] = preg_replace('#&[^=]+=1$#is', "&{$csrfToken}=1", $parseCurlResult['post']);
        return $parseCurlResult;
    });

//3. 登录成功，锁定cookie的更新，直接访问已登录页面内容
$autologin->lockLastCookieFile();
echo $autologin->getUrl('http://www.zjmainstay.cn/administrator/index.php?option=com_modules');
