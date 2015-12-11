<?php

namespace Acme\DataBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Acme\DataBundle\Model\Constants\ValidationType;

class ValidationManager
{
	public static function validate($requestName, $requestItem, $type, $values = array())
    {
        $requestItem = trim($requestItem);

		switch($type) {
			case ValidationType::REQUIRED:
				if(!strlen($requestItem)) {
					throw new \Exception($requestName . " is required.");
				}
				if(!empty($values)) {
					if(!in_array($requestItem, $values))
						throw new \Exception($requestName . " is invalid.");
				}
			break;
			case ValidationType::DATE_:
				if(strlen($requestItem) && date('m/d/Y', strtotime($requestItem)) != $requestItem) {
					throw new \Exception($requestName . " is invalid. (mm/dd/yyyy)");
				}
			break;
			case ValidationType::EMAIL:
				if(strlen($requestItem) && !preg_match("/^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $requestItem)) {
					throw new \Exception($requestName . " is invalid.");
				}
			break;
			case ValidationType::NUMBER:
				if(!strlen($requestItem) || !is_numeric($requestItem) || $requestItem <= 0) {
					throw new \Exception($requestName . " is invalid.");
				}
			break;
			case ValidationType::URL:
				if(strlen($requestItem) && !preg_match("/^(https?:\/\/+[\w\-]+\.[\w\-]+)/i", $requestItem)) {
					throw new \Exception($requestName . " is invalid.");
				}
			break;
			case ValidationType::ZIP:
				if(strlen($requestItem) && !preg_match("/^[0-9]{5}$/", $requestItem)) {
					throw new \Exception($requestName . " is invalid.");
				}
			break;
		}
    }
}
