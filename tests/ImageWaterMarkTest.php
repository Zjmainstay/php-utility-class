<?php
use PhpUtility\ImageWaterMark;
/**
 * phpunit --stderr true tests/ImageWaterMarkTest.php
 */
class ImageWaterMarkTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->imageWaterMarkObj = new ImageWaterMark;
        $this->imagePath = __DIR__ . '/../images/';
    }

    /**
     * 测试解析文件类型正确
     * @dataProvider imagePathData
     */
    public function testGetImageTypeInfo($image, $imageType) {
        $this->assertEquals($this->imageWaterMarkObj->getImageTypeInfo($this->imagePath . $image), $imageType);
    }

    /**
     * 从图片文件创建图片资源
     * @dataProvider imagePathData
     */
    public function testCreateImageFromFile($image, $imageType) {
        $this->assertEquals(gettype($this->imageWaterMarkObj->createImageFromFile($this->imagePath . $image, $imageType)), 'resource');
    }

    /**
     * 测试图片创建
     * @dataProvider imagePathData
     */
    public function testCreateImage($image, $imageType, $waterMarkImage, $waterMarkImageType) {
        $this->assertNotEquals($this->imageWaterMarkObj->createImageWithWaterMark($this->imagePath . $image, $this->imagePath . $waterMarkImage), null);
    }

    /**
     * 测试图片显示、图片存储
     * @dataProvider imagePathData
     */
    public function testShowImage($image, $imageType, $waterMarkImage, $waterMarkImageType) {
        $this->imageWaterMarkObj->createImageWithWaterMark($this->imagePath . $image, $this->imagePath . $waterMarkImage);

        #显示图片
        ob_start();
        $this->imageWaterMarkObj->showImageWithWaterMark();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($content);

        #存储文件
        ob_start();
        $saveFile = $this->imagePath . $image . '.save';
        $this->imageWaterMarkObj->saveImageWithWaterMark($saveFile);
        ob_end_clean();
        $this->assertTrue(file_exists($saveFile));
        unlink($saveFile);
    }

    /**
     * 图片及类型测试
     */
    public function imagePathData() {
        return [
            ['image_jpeg_type.jpg', 'jpeg', 'zjmainstay.cn.watermark.png', 'png'],
            ['image_png_type.png', 'png', 'zjmainstay.cn.watermark.png', 'png'],
            ['image_gif_type.gif', 'gif', 'zjmainstay.cn.watermark.png', 'png'],
        ];
    }


}
