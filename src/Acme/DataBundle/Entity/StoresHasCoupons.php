<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* StoresHasCoupons
*/
class StoresHasCoupons
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var integer
   */
  private $orderIdx;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  private $stores;

  /**
   * @var \Acme\DataBundle\Entity\Coupons
   */
  private $coupons;


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
   * Set orderIdx
   *
   * @param integer $orderIdx
   * @return StoresHasCoupons
   */
  public function setOrderIdx($orderIdx)
  {
    $this->orderIdx = $orderIdx;

    return $this;
  }

  /**
   * Get orderIdx
   *
   * @return integer
   */
  public function getOrderIdx()
  {
    return $this->orderIdx;
  }

  /**
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return StoresHasCoupons
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
   * Set coupons
   *
   * @param \Acme\DataBundle\Entity\Coupons $coupons
   * @return StoresHasCoupons
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
}
