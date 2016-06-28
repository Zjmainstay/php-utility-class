<?php
namespace PhpUtility;
/**
 * @author Zjmainstay
 * @website http://www.zjmainstay.cn
 * @year 2016
 * 图像水印生成类
 */
class ImageWaterMark
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
    protected $_waterMarkImage = null;

    /**
     * 加水印图像资源
     * @var null
     */
    protected $_imageWithWaterMark = null;

    /**
     * 原图图片类型（jpeg/png/gif）
     * @var null
     */
    protected $_imageType = null;

    /**
     * 水印图片类型
     * @var null
     */
    protected $_waterMarkImageType = null;

    /**
     * 创建带水印图像
     * @param        $imagePath
     * @param        $waterMarkImagePath
     * @param string $placeType
     * @param int    $margin
     * @param int    $waterMarkQuality
     * @param int    $waterMarkOpacity
     */
    public function createImageWithWaterMark($imagePath, $waterMarkImagePath, $placeType = 'CENTER', $margin = 0, $waterMarkOpacity = 20) {
        $this->_imageType = $this->getImageTypeInfo($imagePath);
        $this->_image = $this->createImageFromFile($imagePath, $this->_imageType);
        $this->_waterMarkImageType = $this->getImageTypeInfo($waterMarkImagePath);
        $this->_waterMarkImage = $this->createImageFromFile($waterMarkImagePath, $this->_waterMarkImageType);
        if(empty($this->_image) || empty($this->_waterMarkImage)) {
            return null;
        }

        $imageWidth = imagesx($this->_image);
        $imageHeight = imagesy($this->_image);
        
        $waterMarkImageWidth = imagesx($this->_waterMarkImage);
        $waterMarkImageHeight = imagesy($this->_waterMarkImage);
        
        $positionX = $imageWidth - $waterMarkImageWidth;
        $positionY = $imageHeight - $waterMarkImageHeight;
        
        $placeExchangeInfo = $this->getPlaceExchange($placeType, $positionX, $positionY, $margin);

        $this->_imageWithWaterMark = $this->_image;
        
        return imagecopymerge($this->_imageWithWaterMark, $this->_waterMarkImage, $placeExchangeInfo['x'], $placeExchangeInfo['y'], 0, 0, $waterMarkImageWidth, $waterMarkImageHeight, $waterMarkOpacity);
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
    public function showWaterMarkImage() {
        $this->_show($this->_waterMarkImage, $this->_waterMarkImageType);
    }

    /**
     * 显示加水印图片
     */
    public function showImageWithWaterMark() {
        $this->_show($this->_imageWithWaterMark, $this->_imageType);
    }
    
    public function saveImageWithWaterMark($filePath) {
        $this->_show($this->_imageWithWaterMark, $this->_imageType, $filePath);
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
        (gettype($this->_waterMarkImage) == 'resource') && imagedestroy($this->_waterMarkImage);
        (gettype($this->_imageWithWaterMark) == 'resource') && imagedestroy($this->_imageWithWaterMark);
    }
}
