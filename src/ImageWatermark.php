<?php
namespace PhpUtility;
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2016
 * @baseOn https://github.com/andreCatita/imageWatermark
 * 图像水印生成类
 */
class ImageWatermark
{
    /**
     * 原图图像资源
     * @var null
     */
    protected $_image = null;
    
    /**
     * 水印图像资源
     * @var null
     */
    protected $_watermarkImage = null;

    /**
     * 加水印图像资源
     * @var null
     */
    protected $_imageWithWatermark = null;

    /**
     * 原图图片类型（jpeg/png/gif）
     * @var null
     */
    protected $_imageType = null;

    /**
     * 水印图片类型
     * @var null
     */
    protected $_watermarkImageType = null;

    /**
     * 水印重复到原图大小
     * @var null
     */
    protected $_repeatWatermarkImage = null;

    /**
     * 创建带水印图像
     * @param        $imagePath             原图路径
     * @param        $watermarkImagePath    水印图片路径
     * @param string $placeType             水印位置：TOP_LEFT, TOP_CENTER, TOP_RIGHT, CENTER_LEFT, CENTER, CENTER_RIGHT, BOTTOM_LEFT, BOTTOM_CENTER, BOTTOM_RIGHT
     * @param int    $margin                边距
     * @param int    $watermarkOpacity      水印透明度
     * @param bool   repeatWatermark        水印重复（当水印大小小于原图大小时，通过重复得到全背景水印）
     */
    public function createImageWithWatermark($imagePath, $watermarkImagePath, $placeType = 'CENTER', $margin = 0, $watermarkOpacity = 20, $repeatWatermark = false) {
        $imageInfo = $this->getImageInfo($imagePath);
        $watermarkInfo = $this->getImageInfo($watermarkImagePath);

        $this->_imageType = $imageInfo['type'];
        $this->_image = $imageInfo['image'];
        $this->_watermarkImageType = $watermarkInfo['type'];
        $this->_watermarkImage = $watermarkInfo['image'];

        if(empty($this->_image) || empty($this->_watermarkImage)) {
            return null;
        }
        
        //加水印图片拷贝自原图
        $this->_imageWithWatermark = $this->_image;

        if($repeatWatermark) {  //水印重复（全背景水印）
            $placeExchangeInfo = $this->getPlaceExchange($placeType, 0, 0, 0);
            $this->repeatWatermarkImageToImageSize($imagePath, $watermarkImagePath);
            return imagecopymerge($this->_imageWithWatermark, $this->_repeatWatermarkImage, $placeExchangeInfo['x'], $placeExchangeInfo['y'], 0, 0, $imageInfo['width'], $imageInfo['height'], $watermarkOpacity);
        } else {    //单水印
            $positionX = $imageInfo['width'] - $watermarkInfo['width'];
            $positionY = $imageInfo['height'] - $watermarkInfo['height'];
            
            $placeExchangeInfo = $this->getPlaceExchange($placeType, $positionX, $positionY, $margin);
            return imagecopymerge($this->_imageWithWatermark, $this->_watermarkImage, $placeExchangeInfo['x'], $placeExchangeInfo['y'], 0, 0, $watermarkInfo['width'], $watermarkInfo['height'], $watermarkOpacity);
        }
    }

    /**
     * 获取图片信息
     */
    public function getImageInfo($imagePath) {
        $imageType      = $this->getImageTypeInfo($imagePath);
        $image          = $this->createImageFromFile($imagePath, $imageType);
        if(empty($image)) {
            return false;
        }
        $imageWidth     = imagesx($image);
        $imageHeight    = imagesy($image);

        return [
            'type'      => $imageType,
            'image'     => $image,
            'width'     => $imageWidth,
            'height'    => $imageHeight,
        ];
    }

    /**
     * 重复水印得到一个与原图等大小的水印图（全背景水印）
     */
    public function repeatWatermarkImageToImageSize($imagePath, $watermarkImagePath) {
        $imageInfo = $this->getImageInfo($imagePath);
        $watermarkInfo = $this->getImageInfo($watermarkImagePath);

        if(empty($imageInfo) || empty($watermarkInfo)) {
            return false;
        }

        //创建与原图等大小的透明背景图
        $this->_repeatWatermarkImage = $this->createTransparentImage($imageInfo['width'], $imageInfo['height']);

        //逐行逐列填充水印，创造全背景水印
        $cols = ceil($imageInfo['width'] / $watermarkInfo['width']);
        $rows = ceil($imageInfo['height'] / $watermarkInfo['height']);
        $res  = true;
        for($w = 0; $w < $cols; $w++) {
            $positionX = $w * $watermarkInfo['width'];  //x轴为按水印宽度右移的结果
            for($h = 0; $h < $rows; $h++) {
                $positionY = $h * $watermarkInfo['height']; //y轴为按水印高度下移的结果
                $res = $res && imagecopymerge($this->_repeatWatermarkImage, $watermarkInfo['image'], $positionX, $positionY, 0, 0, $watermarkInfo['width'], $watermarkInfo['height'], 100);
            }
        }

        // $this->_show($this->_repeatWatermarkImage, 'png');

        return $res;
    }

    public function createTransparentImage($width, $height) {
        //创建一个图
        $image = imagecreatetruecolor($width, $height);
        //创建透明背景色，主要127参数，其他可以0-255，因为任何颜色的透明都是透明
        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        //指定颜色为透明（做了移除测试，发现没问题）
        imagecolortransparent($image, $transparent);
        //保留透明颜色
        imagesavealpha($image, true);
        //填充图片颜色
        imagefill($image, 0, 0, $transparent);

        return $image;
    }

    /**
     * 通过文件路径获取图片类型
     * @param $imagePath
     *
     * @return null|string
     */
    public function getImageTypeInfo($imagePath) {
        list(,, $image_type) = getimagesize($imagePath);

        if ($image_type === NULL) {
            return null;
        }

        switch ($image_type) {
            case IMAGETYPE_GIF:
                return 'gif';
                break;
            case IMAGETYPE_JPEG:
                return 'jpeg';
                break;
            case IMAGETYPE_PNG:
                return 'png';
                break;
            default:
                return null;
                break;
        }
    }

    /**
     * 利用图片路径创建图片资源对象
     * @param $imagePath 图片路径
     * @return resource|null
     */
    public function createImageFromFile($imagePath, $imageType) {
        
        $func = "imagecreatefrom{$imageType}";
        
        if(!function_exists($func)) {
            return null;
        }
        
        return $func($imagePath);
    }

    /**
     * 获取位置类型
     * @return array
     */
    public function getPlaceExchange($type, $positionX, $positionY, $margin) {
        $placeExchangeMap = [
            'TOP_LEFT'      => [
                'x' => (0 + $margin),
                'y' => (0 + $margin), 
            ],
            'TOP_CENTER'    => [
                'x' => floor($positionX / 2),
                'y' => (0 + $margin), 
            ],
            'TOP_RIGHT'     => [
                'x' => ($positionX - $margin),
                'y' => (0 + $margin), 
            ],
            'CENTER_LEFT'   => [
                'x' => (0 + $margin),
                'y' => floor($positionY / 2), 
            ],
            'CENTER'        => [
                'x' => floor($positionX / 2),
                'y' => floor($positionY / 2), 
            ],
            'CENTER_RIGHT'  => [
                'x' => ($positionX - $margin),
                'y' => floor($positionY / 2), 
            ],
            'BOTTOM_LEFT'   => [
                'x' => (0 + $margin),
                'y' => ($positionY - $margin), 
            ],
            'BOTTOM_CENTER' => [
                'x' => floor($positionX / 2),
                'y' => ($positionY - $margin), 
            ],
            'BOTTOM_RIGHT'  => [
                'x' => ($positionX - $margin),
                'y' => ($positionY - $margin), 
            ],
        ];
        
        $type = strtoupper($type);
        
        if(!isset($placeExchangeMap[$type])) {
            $type = 'CENTER';
        }
        
        return $placeExchangeMap[$type];
    }

    /**
     * 显示原图
     */
    public function showImage() {
        $this->_show($this->_image, $this->_imageType);
    }

    /**
     * 显示水印
     */
    public function showWatermarkImage() {
        $this->_show($this->_watermarkImage, $this->_watermarkImageType);
    }

    /**
     * 显示加水印图片
     */
    public function showImageWithWatermark() {
        $this->_show($this->_imageWithWatermark, $this->_imageType);
    }
    
    public function saveImageWithWatermark($filePath) {
        $this->_show($this->_imageWithWatermark, $this->_imageType, $filePath);
    }

    /**
     * 输出图片
     * @param $image
     * @param $type
     *
     * @return bool
     */
    protected function _show($image, $type, $filePath = null) {
        header("Content-Type: image/{$type}");
        $func = "image{$type}";
        $func($image, $filePath);
    }

    /**
     * 销毁图片资源，释放内存
     */
    public function __destruct() {
        (gettype($this->_image) == 'resource') && imagedestroy($this->_image);
        (gettype($this->_watermarkImage) == 'resource') && imagedestroy($this->_watermarkImage);
        (gettype($this->_imageWithWatermark) == 'resource') && imagedestroy($this->_imageWithWatermark);
    }
}
