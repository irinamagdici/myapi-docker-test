<?php

namespace Acme\CacheManagementBundle\Controller;

/**********************************************************************************************************************************
Application
**********************************************************************************************************************************/
use Symfony\Component\DependencyInjection\ContainerInterface;
use Acme\CacheManagementBundle\Doctrine\Cache\RedisCache;
use Predis\Client;

class CacheService
{

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

    public function initiateCache() {

    	$predis = new RedisCache();
        $predis->setRedis(new Client());

        return $predis;
    }
}
