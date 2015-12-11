<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* PromotionsHasCoupons
*/
class PromotionsHasCoupons
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var \Acme\DataBundle\Entity\Promotions
   */
  private $promotions;

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
   * Set promotions
   *
   * @param \Acme\DataBundle\Entity\Promotions $promotions
   * @return PromotionsHasCoupons
   */
  public function setPromotions(\Acme\DataBundle\Entity\Promotions $promotions = null)
  {
    $this->promotions = $promotions;

    return $this;
  }

  /**
   * Get promotions
   *
   * @return \Acme\DataBundle\Entity\Promotions
   */
  public function getPromotions()
  {
    return $this->promotions;
  }

  /**
   * Set coupons
   *
   * @param \Acme\DataBundle\Entity\Coupons $coupons
   * @return PromotionsHasCoupons
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
