<?php

namespace Acme\StorageBundle\Controller;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Acme\DataBundle\Model\Constants\UtilsConstants;
use Acme\StorageBundle\Model\S3;

class ImageService {

  private $allowedExtensions = array(".jpg", ".png", ".gif", ".jpeg");

	public function __construct($container, $templateEngine) {
    $this->container = $container;
    $this->templateEngine = $templateEngine;
  }

  private function get($service) {
  	return $this->container->get($service);
  }

  private function getExtension($fileName) {
      return strtolower(substr($fileName, strrpos($fileName, '.')));
  }

  private function isImageValid($imageName) {
  	if(!in_array($this->getExtension($imageName), $this->allowedExtensions)) return false;
      return true;
  }

  private function saveImage($img, $imageType) {

    switch($imageType) {
      case 'COUPON_MEDIUM':
      case 'COUPON_PRINT':
        $photo_name = $img['name'];

        $target = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . $photo_name;
        move_uploaded_file($img['tmp_name'], $target);
      break;
      case 'TODO':
        //get image extension
        $ext = $this->getExtension($img['name']);
        //generate a name for image
        $photo_name = md5(rand(1,10000).time()) . $ext;

        //get settings from parameters
        $h_resized = $this->container->parameters['project']['resize_height'];
        $target = $this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . $photo_name;

        //get image dimensions
        if(getimagesize($img['tmp_name']))
          list($width, $height) = getimagesize($img['tmp_name']);

        //create thumb
        if($width > $h_resized && $height > $h_resized) {

          $thumb = imagecreatetruecolor($h_resized, $h_resized);

          switch($ext) {
            case '.jpg':
              $myImage = imagecreatefromjpeg($img['tmp_name']);
            break;
            case '.jpeg':
              $myImage = imagecreatefromjpeg($img['tmp_name']);
            break;
            case '.png':
              $myImage = imagecreatefrompng($img['tmp_name']);
              imagesavealpha($thumb, true);
              imagealphablending($thumb, false);
            break;
            case '.gif':
              $myImage = imagecreatefromgif($img['tmp_name']);
            break;
          }

          //calculating the part of the image to use for thumbnail
          if($width >= $height) {
            $y = 0;
            $x = ($width - $height) / 2;
            $smallestSide = $height;
          } else {
            $x = 0;
            $y = ($height - $width) / 2;
            $smallestSide = $width;
          }

          //copying the part into thumbnail
          imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $h_resized, $h_resized, $smallestSide, $smallestSide);

          switch($ext) {
            case '.jpg':
              imagejpeg($thumb, $target, 85);
            break;
            case '.jpeg':
              imagejpeg($thumb, $target, 85);
            break;
            case '.png':
              imagepng($thumb, $target);
            break;
            case '.gif':
              imagegif($thumb, $target);
            break;
          }

          imagedestroy($thumb);
          imagedestroy($myImage);
        }
        else {
          move_uploaded_file($img['tmp_name'], $target);
        }
      break;
    }

    return $photo_name;

  }

/**********************************************************************************************************************************
Public Methods
**********************************************************************************************************************************/
  public function uploadImageToAS3($fileObject, $imageType) {

    //check extension
    if(!$this->isImageValid($fileObject['name']))
      throw new \Exception("Please check image extension (valid extensions are: jpg, jpeg, png, gif).");

    //check other things based on image Type
    switch($imageType) {

      case 'COUPON_MEDIUM':
        //check file name
        if(strpos($fileObject['name'], '-med') === FALSE)
          throw new \Exception("Please check image filename (valid filename is *-med.extension).");

        //check width and height
        $width = $height = 0;
        if(getimagesize($fileObject['tmp_name']))
          list($width, $height) = getimagesize($fileObject['tmp_name']);
          if($width != UtilsConstants::WIDTH || $height != UtilsConstants::HEIGHT)
            throw new \Exception("Please check image width and height (valid dimension is " . UtilsConstants::WIDTH . " x " . UtilsConstants::HEIGHT . ").");

        //photo
        $photo_name = $this->saveImage($fileObject, $imageType);
      break;

      case 'COUPON_PRINT':
        //check file name
        if(strpos($fileObject['name'], '-lg') === FALSE)
          throw new \Exception("Please check image filename (valid filename is *-lg.extension).");

        //photo
        $photo_name = $this->saveImage($fileObject, $imageType);
      break;

    }

    //initiate S3
    $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);

    if($imageType === 'COUPON_MEDIUM' || $imageType === 'COUPON_PRINT')
      $cdnDirectory = $this->container->parameters['storage']['couponsDirectory'];
    else
      $cdnDirectory = $this->container->parameters['storage']['imagesDirectory'];


    //upload image to CDN failed
    if(!$s3->putObjectFile($this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . $photo_name, $this->container->parameters['storage']['amazon_s3_bucket_name'], $cdnDirectory . $photo_name, S3::ACL_PUBLIC_READ))
      throw new \Exception("Upload failed. Image cannot be uploaded to CDN.");

    //delete local file
    unlink($this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_images'] . $photo_name);

    return $photo_name;

  }
}
