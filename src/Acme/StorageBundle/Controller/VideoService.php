<?php

namespace Acme\StorageBundle\Controller;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Acme\StorageBundle\Model\S3;
use Acme\StorageBundle\Model\ElasticTranscoder;

class VideoService
{

    private $allowedExtensions = array(".mp4", ".ogv", ".wmv", ".avi", ".mpg", ".mpeg", ".mov");

	public function __construct($container, $templateEngine) {
        $this->container = $container;
        $this->templateEngine = $templateEngine;
    }

    private function get($service){
    	return $this->container->get($service);
    }

    private function getExtension($fileName) {
        return strtolower(substr($fileName, strrpos($fileName, '.')));
    }

    private function isVideoValid($videoName){
    	if(!in_array($this->getExtension($videoName), $this->allowedExtensions)) return false;
        return true;
    }

    private function submitVideoForConversion($videoName, $videoFullName) {

        //initiate ElasticTranscoder
        $et = new ElasticTranscoder($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], $this->container->parameters['storage']['amazon_transcoder_region']);

        //get conversion settings
        $pipelineId = $this->container->parameters['storage']['amazon_transcoder_pipeline'];
        $input = array('Key' => $this->container->parameters['storage']['videosDirectory'] . $videoFullName);
        $output_mp4 = array(
            'Key' => $this->container->parameters['storage']['videosDirectory'] . $videoName . ".mp4",
            'PresetId' => $this->container->parameters['storage']['amazon_transcoder_mp4']
        );
        $output_webm = array(
            'Key' => $this->container->parameters['storage']['videosDirectory'] . $videoName . ".webm",
            'PresetId' => $this->container->parameters['storage']['amazon_transcoder_webm']
        );

        //create conversion jobs
        if($this->getExtension($videoFullName) == ".mp4")
            $result = ElasticTranscoder::createJob($input, array($output_webm), $pipelineId);
        else if($this->getExtension($videoFullName) == ".webm")
            $result = ElasticTranscoder::createJob($input, array($output_mp4), $pipelineId);
        else
            $result = ElasticTranscoder::createJob($input, array($output_mp4, $output_webm), $pipelineId);

        return $result;
    }

/**********************************************************************************************************************************
Public Methods
**********************************************************************************************************************************/
    public function uploadVideoToAS3($fileObject) {

        //check extension
        if(!$this->isVideoValid($fileObject['name']))
            throw new \Exception("Please check video extension (valid extensions are: mp4, ogv, wmv, avi, mpg, mpeg, mov).");

        //initiate S3
        $s3 = new S3($this->container->parameters['storage']['amazon_aws_access_key'], $this->container->parameters['storage']['amazon_aws_security_key'], false, $this->container->parameters['storage']['amazon_s3_endpoint']);

        //generate video name
        $video_name = md5(rand(1,10000).time());
        $video_full_name = $video_name . $this->getExtension($fileObject['name']);

        //upload video to CDN failed
        if(!$s3->putObjectFile($fileObject['tmp_name'], $this->container->parameters['storage']['amazon_s3_bucket_name'], $this->container->parameters['storage']['videosDirectory'] . $video_full_name, S3::ACL_PUBLIC_READ))
            throw new \Exception("Upload failed. Video cannot be uploaded to CDN.");

        //start job conversion for the video uploaded
        $transcoder = $this->submitVideoForConversion($video_name, $video_full_name);

        //conversion error
        if(!$transcoder)
            throw new \Exception(ElasticTranscoder::getErrorMsg());

        return array($video_full_name, $transcoder['Job']['Id']);

    }
}
