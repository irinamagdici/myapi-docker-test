<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* StoresHasServices
*/
class StoresHasServices
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $description;

  /**
   * @var boolean
   */
  private $isFeatured;

  /**
   * @var integer
   */
  private $orderFeaturedIdx;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  private $stores;

  /**
   * @var \Acme\DataBundle\Entity\Services
   */
  private $services;


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
   * Set description
   *
   * @param string $description
   * @return StoresHasServices
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set isFeatured
   *
   * @param boolean $isFeatured
   * @return StoresHasServices
   */
  public function setIsFeatured($isFeatured)
  {
    $this->isFeatured = $isFeatured;

    return $this;
  }

  /**
   * Get isFeatured
   *
   * @return boolean
   */
  public function getIsFeatured()
  {
    return $this->isFeatured;
  }

  /**
   * Set orderFeaturedIdx
   *
   * @param integer $orderFeaturedIdx
   * @return StoresHasServices
   */
  public function setOrderFeaturedIdx($orderFeaturedIdx)
  {
    $this->orderFeaturedIdx = $orderFeaturedIdx;

    return $this;
  }

  /**
   * Get orderFeaturedIdx
   *
   * @return integer
   */
  public function getOrderFeaturedIdx()
  {
    return $this->orderFeaturedIdx;
  }

  /**
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return StoresHasServices
   */
  public function setStores(\Acme\DataBundle\Entity\Stores $stores = null)
  {
    $this->stores = $stores;

    return $this;
  }

  /**
   * Get stores
   *
   * @return \Acme\DataBundle\Entity\Stores
   */
  public function getStores()
  {
    return $this->stores;
  }

  /**
   * Set services
   *
   * @param \Acme\DataBundle\Entity\Services $services
   * @return StoresHasServices
   */
  public function setServices(\Acme\DataBundle\Entity\Services $services = null)
  {
    $this->services = $services;

    return $this;
  }

  /**
   * Get services
   *
   * @return \Acme\DataBundle\Entity\Services
   */
  public function getServices()
  {
    return $this->services;
  }
}
