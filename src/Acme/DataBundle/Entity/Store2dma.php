<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Store2dma
 */
class Store2dma
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $storeid;

    /**
     * @var integer
     */
    private $dmaid;

    /**
     * @var string
     */
    private $storeEmail;


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
     * Set storeid
     *
     * @param integer $storeid
     * @return Store2dma
     */
    public function setStoreid($storeid)
    {
        $this->storeid = $storeid;

        return $this;
    }

    /**
     * Get storeid
     *
     * @return integer 
     */
    public function getStoreid()
    {
        return $this->storeid;
    }

    /**
     * Set dmaid
     *
     * @param integer $dmaid
     * @return Store2dma
     */
    public function setDmaid($dmaid)
    {
        $this->dmaid = $dmaid;

        return $this;
    }

    /**
     * Get dmaid
     *
     * @return integer 
     */
    public function getDmaid()
    {
        return $this->dmaid;
    }

    /**
     * Set storeEmail
     *
     * @param string $storeEmail
     * @return Store2dma
     */
    public function setStoreEmail($storeEmail)
    {
        $this->storeEmail = $storeEmail;

        return $this;
    }

    /**
     * Get storeEmail
     *
     * @return string 
     */
    public function getStoreEmail()
    {
        return $this->storeEmail;
    }
}
