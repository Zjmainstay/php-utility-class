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
     * 创建带水印图像
     * @param        $imagePath
     * @param        $watermarkImagePath
     * @param string $placeType
     * @param int    $margin
     * @param int    $watermarkQuality
     * @param int    $watermarkOpacity
     */
    public function createImageWithWatermark($imagePath, $watermarkImagePath, $placeType = 'CENTER', $margin = 0, $watermarkOpacity = 20) {
        $this->_imageType = $this->getImageTypeInfo($imagePath);
        $this->_image = $this->createImageFromFile($imagePath, $this->_imageType);
        $this->_watermarkImageType = $this->getImageTypeInfo($watermarkImagePath);
        $this->_watermarkImage = $this->createImageFromFile($watermarkImagePath, $this->_watermarkImageType);
        if(empty($this->_image) || empty($this->_watermarkImage)) {
            return null;
        }

        $imageWidth = imagesx($this->_image);
        $imageHeight = imagesy($this->_image);
        
        $watermarkImageWidth = imagesx($this->_watermarkImage);
        $watermarkImageHeight = imagesy($this->_watermarkImage);
        
        $positionX = $imageWidth - $watermarkImageWidth;
        $positionY = $imageHeight - $watermarkImageHeight;
        
        $placeExchangeInfo = $this->getPlaceExchange($placeType, $positionX, $positionY, $margin);

        $this->_imageWithWatermark = $this->_image;
        
        return imagecopymerge($this->_imageWithWatermark, $this->_watermarkImage, $placeExchangeInfo['x'], $placeExchangeInfo['y'], 0, 0, $watermarkImageWidth, $watermarkImageHeight, $watermarkOpacity);
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
