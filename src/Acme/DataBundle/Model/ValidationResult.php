<?php

namespace Acme\DataBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class ValidationResult
{
	public $isSuccess;
	public $errorMessage;

	public function __construct()
    {
        $this->isSuccess = true;
        $this->errorMessage = '';
    }

    public function setError($message){
    	$this->isSuccess = false;
    	$this->errorMessage = $message;
    }
}
