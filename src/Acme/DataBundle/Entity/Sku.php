<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sku
 */
class Sku
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $skuCode;

    /**
     * @var string
     */
    private $displayName;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set skuCode
     *
     * @param string $skuCode
     * @return Sku
     */
    public function setSkuCode($skuCode)
    {
        $this->skuCode = $skuCode;

        return $this;
    }

    /**
     * Get skuCode
     *
     * @return string
     */
    public function getSkuCode()
    {
        return $this->skuCode;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return Sku
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
}
