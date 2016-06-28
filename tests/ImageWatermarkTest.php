<?php
use PhpUtility\ImageWatermark;
/**
 * phpunit --stderr true tests/ImageWatermarkTest.php
 */
class ImageWatermarkTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        $this->imageWatermarkObj = new ImageWatermark;
        $this->imagePath = __DIR__ . '/../images/';
    }

    /**
     * 测试解析文件类型正确
     * @dataProvider imagePathData
     */
    public function testGetImageTypeInfo($image, $imageType) {
        $this->assertEquals($this->imageWatermarkObj->getImageTypeInfo($this->imagePath . $image), $imageType);
    }

    /**
     * 从图片文件创建图片资源
     * @dataProvider imagePathData
     */
    public function testCreateImageFromFile($image, $imageType) {
        $this->assertEquals(gettype($this->imageWatermarkObj->createImageFromFile($this->imagePath . $image, $imageType)), 'resource');
    }

    /**
     * 测试图片创建
     * @dataProvider imagePathData
     */
    public function testCreateImage($image, $imageType, $watermarkImage, $watermarkImageType) {
        $this->assertNotEquals($this->imageWatermarkObj->createImageWithWatermark($this->imagePath . $image, $this->imagePath . $watermarkImage), null);
    }

    /**
     * 测试图片显示、图片存储
     * @dataProvider imagePathData
     */
    public function testShowImage($image, $imageType, $watermarkImage, $watermarkImageType) {
        $this->imageWatermarkObj->createImageWithWatermark($this->imagePath . $image, $this->imagePath . $watermarkImage);

        #显示图片
        ob_start();
        $this->imageWatermarkObj->showImageWithWatermark();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertNotEmpty($content);

        #存储文件
        ob_start();
        $saveFile = $this->imagePath . $image . '.save';
        $this->imageWatermarkObj->saveImageWithWatermark($saveFile);
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
