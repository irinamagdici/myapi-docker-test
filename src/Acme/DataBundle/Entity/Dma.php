<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dma
 */
class Dma
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $title;

  /**
   * @var string
   */
  private $state;

  /**
   * @var string
   */
  private $dmaSlug;

  /**
   * @var string
   */
  private $lat;

  /**
   * @var string
   */
  private $lng;

  /**
   * @var \DateTime
   */
  private $dateUpdated;

  /**
   * @var \DateTime
   */
  private $dateCreated;


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
   * Set title
   *
   * @param string $title
   * @return Dma
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set state
   *
   * @param string $state
   * @return Dma
   */
  public function setState($state)
  {
    $this->state = $state;

    return $this;
  }

  /**
   * Get state
   *
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }

  /**
   * Set dmaSlug
   *
   * @param string $dmaSlug
   * @return Dma
   */
  public function setDmaSlug($dmaSlug)
  {
    $this->dmaSlug = $dmaSlug;

    return $this;
  }

  /**
   * Get dmaSlug
   *
   * @return string
   */
  public function getDmaSlug()
  {
    return $this->dmaSlug;
  }

  /**
   * Set lat
   *
   * @param string $lat
   * @return Dma
   */
  public function setLat($lat)
  {
    $this->lat = $lat;

    return $this;
  }

  /**
   * Get lat
   *
   * @return string
   */
  public function getLat()
  {
    return $this->lat;
  }

  /**
   * Set lng
   *
   * @param string $lng
   * @return Dma
   */
  public function setLng($lng)
  {
    $this->lng = $lng;

    return $this;
  }

  /**
   * Get lng
   *
   * @return string
   */
  public function getLng()
  {
    return $this->lng;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Dma
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
   * @return Dma
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
}
