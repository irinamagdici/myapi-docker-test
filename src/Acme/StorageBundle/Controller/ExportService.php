<?php

namespace Acme\StorageBundle\Controller;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acme\StorageBundle\Model\S3;

class ExportService {

	public function __construct($container, $templateEngine) {
    $this->container = $container;
    $this->templateEngine = $templateEngine;
  }

  private function get($service) {
  	return $this->container->get($service);
  }

/**********************************************************************************************************************************
Public Methods
**********************************************************************************************************************************/

  public function uploadCSVToAS3($data) {

    //generate a name for CSV
    $fileCSV = md5(time()) . "-export.csv";

    //write data in CSV
    $fp = fopen($this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . $fileCSV, 'w');

    fputcsv($fp, array_keys($data[0]));
    foreach($data as $fields) {
      fputcsv($fp, $fields);
    }

    fclose($fp);

    //initiate S3
    $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);

    //upload CSV to CDN failed
    if(!$s3->putObjectFile($this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . $fileCSV, $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['documentsDirectory'] . $fileCSV, S3::ACL_PUBLIC_READ))
      throw new \Exception("Upload failed. CSV cannot be uploaded to CDN.");

    //delete local file
    unlink($this->container->parameters['project']['site_path'] . $this->container->parameters['project']['upload_dir_documents'] . $fileCSV);

    return $fileCSV;

  }
}
