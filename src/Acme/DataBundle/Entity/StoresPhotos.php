<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* StoresPhotos
*/
class StoresPhotos
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @var integer
   */
  private $orderIdx;

  /**
   * @var \DateTime
   */
  private $dateUpdated;

  /**
   * @var \DateTime
   */
  private $dateCreated;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  private $stores;


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
   * Set name
   *
   * @param string $name
   * @return StoresPhotos
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set orderIdx
   *
   * @param integer $orderIdx
   * @return StoresPhotos
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
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return StoresPhotos
   */
  public function setDateUpdated($dateUpdated)
  {
    $this->dateUpdated = $dateUpdated;

    return $this;
  }

  /**
   * Get dateUpdated
   *
   * @return \DateTime
   */
  public function getDateUpdated()
  {
    return $this->dateUpdated;
  }

  /**
   * Set dateCreated
   *
   * @param \DateTime $dateCreated
   * @return StoresPhotos
   */
  public function setDateCreated($dateCreated)
  {
    $this->dateCreated = $dateCreated;

    return $this;
  }

  /**
   * Get dateCreated
   *
   * @return \DateTime
   */
  public function getDateCreated()
  {
    return $this->dateCreated;
  }

  /**
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return StoresPhotos
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
}
