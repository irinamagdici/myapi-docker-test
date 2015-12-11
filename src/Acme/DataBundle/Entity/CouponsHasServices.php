<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* CouponsHasServices
*/
class CouponsHasServices
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var \Acme\DataBundle\Entity\Coupons
   */
  private $coupons;

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
   * Set coupons
   *
   * @param \Acme\DataBundle\Entity\Coupons $coupons
   * @return CouponsHasServices
   */
  public function setCoupons(\Acme\DataBundle\Entity\Coupons $coupons = null)
  {
    $this->coupons = $coupons;

    return $this;
  }

  /**
   * Get coupons
   *
   * @return \Acme\DataBundle\Entity\Coupons
   */
  public function getCoupons()
  {
    return $this->coupons;
  }

  /**
   * Set services
   *
   * @param \Acme\DataBundle\Entity\Services $services
   * @return CouponsHasServices
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
