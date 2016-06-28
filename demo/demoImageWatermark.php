<?php

require_once __DIR__.'/../vendor/autoload.php';

// Configuration
$options = array(
    'WATERMARK_IMAGE'	    => 'zjmainstay.cn.watermark.png',		// The location and name of the watermark  (If using ready a ready .PNG or .GIF set WATERMARK_IS_READY to TRUE)
    'WATERMARK_OPACITY'	    => '20',			// The opacity the image will be merged with, this doesn't apply to WATERMARK_IS_READY
    'WATERMARK_PLACE'	    => 'CENTER',		// This value accepts -> BOTTOM_RIGHT, BOTTOM_LEFT, BOTTOM_CENTER, TOP_CENTER, TOP_LEFT, TOP_RIGHT, CENTER, CENTER_LEFT, CENTER_RIGHT
    'WATERMARK_MARGIN'	    => '10',
);

// Overwrite Defaults
$image				= (isset($_GET['image'])		? $_GET['image']	    : null);
$watermarkImage		= (isset($_GET['watermark'])	? $_GET['watermark']	: $options['WATERMARK_IMAGE']);
$place				= (isset($_GET['place'])		? $_GET['place']	    : $options['WATERMARK_PLACE']);
$margin				= (isset($_GET['margin'])		? $_GET['margin']	    : $options['WATERMARK_MARGIN']);
$opacity        	= (isset($_GET['opacity'])  	? $_GET['opacity']      : $options['WATERMARK_OPACITY']);

$basePath = realpath(__DIR__ . '/..');
$imagePath = realpath(__DIR__ . '/' . ltrim($image, '/'));
$watermarkImagePath = realpath(__DIR__ . '/' . ltrim($watermarkImage, '/'));
if((stripos($imagePath, $basePath) === false) || (stripos($watermarkImagePath, $basePath) === false)) {
    exit('Deny');
}

$imageWatermark = new PhpUtility\ImageWaterMark;

if($imageWatermark->createImageWithWaterMark($image, $watermarkImage, $place, $margin, $opacity)) {
	$imageWatermark->showImageWithWaterMark();
}
