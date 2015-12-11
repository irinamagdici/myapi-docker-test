<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* PipelineSubscribers
*/
class PipelineSubscribers
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $email;

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
   * Set email
   *
   * @param string $email
   * @return PipelineSubscribers
   */
  public function setEmail($email)
  {
    $this->email = $email;

    return $this;
  }

  /**
   * Get email
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return PipelineSubscribers
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
   * @return PipelineSubscribers
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
   * @return PipelineSubscribers
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
