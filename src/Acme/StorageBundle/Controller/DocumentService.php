<?php

namespace Acme\StorageBundle\Controller;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acme\StorageBundle\Model\S3;

class DocumentService {

	private $allowedExtensionsDocument = array(".pdf");
  private $allowedExtensionsCouponsCMS = array(".csv");

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

  private function isDocumentValid($documentName, $documentType) {

    switch($documentType) {
      case 'CV':
      case 'PRINT':
        if(!in_array($this->getExtension($documentName), $this->allowedExtensionsDocument)) return false;
      break;
      case 'STORES_COUPONS_CMS':
      case 'BATCH_UPLOAD_CMS':
        if(!in_array($this->getExtension($documentName), $this->allowedExtensionsCouponsCMS)) return false;
      break;
    }

    return true;
  }

  private function parseCSV($csv) {

    set_time_limit(0);

    ini_set('auto_detect_line_endings', TRUE);

    $array = $fields = array(); $i = 0;
    $handle = @fopen($csv, "r");
    if($handle) {
      while(($row = fgetcsv($handle, 4096)) !== false) {
        if(empty($fields)) {
          $fields = $row;
          continue;
        }
        foreach($row as $k=>$value) {
          $array[$i][$fields[$k]] = $value;
        }
        $i++;
      }

      if(!feof($handle)) {
        throw new \Exception("Error parsing CSV.");
      }

      fclose($handle);
    }

    return $array;
}


/**********************************************************************************************************************************
Public Methods
**********************************************************************************************************************************/
  public function uploadDocumentToAS3($fileObject, $documentType) {

    //check extension
    if(!$this->isDocumentValid($fileObject['name'], $documentType)) {
      switch($documentType) {
        case 'CV':
        case 'PRINT':
          $extensions = implode(", ", $this->allowedExtensionsDocument);
        break;
        case 'STORES_COUPONS_CMS':
          $extensions = implode(", ", $this->allowedExtensionsCouponsCMS);
        break;
      }

      throw new \Exception("Please check document extension (valid extensions are: " . $extensions . ").");
    }

    switch($documentType) {
      case 'CV':
      case 'PRINT':
        //initiate S3
        $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);

        //generate document name
        $document_name = md5(rand(1,10000) . time());
        $document_full_name = $document_name . $this->getExtension($fileObject['name']);

        if(!$s3->putObjectFile($fileObject['tmp_name'], $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['documentsDirectory'] . $document_full_name, S3::ACL_PUBLIC_READ))
          throw new \Exception("Upload failed. Document cannot be uploaded to CDN.");

        return $document_full_name;
      break;
      case 'STORES_COUPONS_CMS':
        //parse CSV
        $csv = $this->parseCSV($fileObject['tmp_name']);

        $storesIds = array();
        for($i=0;$i<count($csv);$i++) {
          if(!isset($csv[$i]['ShopNumber']))
            throw new \Exception("Please check CSV structure. Shop Numbers cannot be found.");
          else {
            if(strlen($csv[$i]['ShopNumber']))
              $storesIds[] = $csv[$i]['ShopNumber'];
          }
        }

        if(empty($storesIds))
          throw new \Exception("Please check CSV structure. Shop Numbers cannot be found.");

        return implode(",", $storesIds);

      break;
      case 'BATCH_UPLOAD_CMS':
        //parse CSV
        $csv = $this->parseCSV($fileObject['tmp_name']);

        return base64_encode(serialize($csv));

      break;
    }

  }
}
