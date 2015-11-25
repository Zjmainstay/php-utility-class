<?php
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
class VarAccessTest extends PHPUnit_Framework_TestCase {
    // 一维数组
    // 键值不存在，没有设置默认值：预期 $getVar = null
    public function testLinearArrayWithNoKeyAndNoDefault() {
        $arr = [];
        $getVar = VarAccess::get($arr, "key");
        
        $this->assertEquals(null, $getVar);
    }

    // 键值不存在，有设置默认值：预期 $getVar = $default
    public function testLinearArrayWithNoKeyAndHasDefault() {
        $arr = [];
        $default = "defaultValue";
        $getVar = VarAccess::get($arr, "key", $default);
        
        $this->assertEquals($default, $getVar);
    }

    // 键值存在，没有设置默认值：预期 $getVar = 键值对应值
    public function testLinearArrayWithKeyAndNoDefault() {
        $arr = ["key" => "value"];
        $getVar = VarAccess::get($arr, "key");

        $this->assertEquals($arr["key"], $getVar);
    }

    // 键值存在，有设置默认值：预期 $getVar = 键值对应值
    public function testLinearArrayWithKeyAndHasDefault() {
        $arr = ["key" => "value"];
        $default = "defaultValue";
        $getVar = VarAccess::get($arr, "key", $default);

        $this->assertEquals($arr["key"], $getVar);
    }

    // 二维数组
    // 一维键值不存在，没有设置默认值：预期 $getVar = null
    public function testTwoDimensionArrayWithNotMatchAndNoDefault() {
        $arr = [];
        $getVar = VarAccess::get($arr, "key1,key2");

        $this->assertEquals(null, $getVar);
    }

    // 一维键值不存在，有设置默认值：预期 $getVar = $default
    public function testTwoDimensionArrayWithNotMatchAndHasDefault() {
        $arr = [];
        $default = "defaultValue";
        $getVar = VarAccess::get($arr, "key1,key2", $default);

        $this->assertEquals($default, $getVar);
    }

    // 一维键值存在，二维键值不存在，没有设置默认值：预期 $getVar = null
    public function testTwoDimensionArrayWithMatchLinearButNotDimensionAndNoDefault() {
        $arr = ["key1" => "value1"];
        $getVar = VarAccess::get($arr, "key1,key2");

        $this->assertEquals(null, $getVar);
    }

    // 一维键值存在，二维键值不存在，有设置默认值：预期 $getVar = $default
    public function testTwoDimensionArrayWithMatchLinearButNotDimensionAndHasDefault() {
        $arr = ["key1" => "value"];
        $default = "defaultValue";
        $getVar = VarAccess::get($arr, "key1,key2", $default);

        $this->assertEquals($default, $getVar);
    }

    // 一维键值存在，二维键值存在，没有设置默认值：预期 $getVar = 键值对应值
    public function testTwoDimensionArrayWithMatchAndNoDefault() {
        $arr = ["key1" => ["key2" => "value2"]];
        $getVar = VarAccess::get($arr, "key1,key2");
        $this->assertEquals($arr["key1"]["key2"], $getVar);
    }

    // 一维键值存在，二维键值存在，有设置默认值：预期 $getVar = 键值对应值
    public function testTwoDimensionArrayWithMatchAndHasDefault() {
        $arr = ["key1" => ["key2" => "value2"]];
        $default = "defaultValue";
        $getVar = VarAccess::get($arr, "key1,key2", $default);

        $this->assertEquals($arr["key1"]["key2"], $getVar);
    }

    //键值带. 使用其他分隔符作为键值分隔符
    public function testSpecialSeparator() {
        $arr = ["key.name" => "value"];
        $getVar = VarAccess::get($arr, "key.name", null, '_');

        $this->assertEquals($arr["key.name"], $getVar);
    }
}
