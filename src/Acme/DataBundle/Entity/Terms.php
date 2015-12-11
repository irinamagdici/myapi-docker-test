<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Terms
*/
class Terms
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $slug;

  /**
   * @var string
   */
  private $term;

  /**
   * @var string
   */
  private $description;

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
   * Set slug
   *
   * @param string $slug
   * @return Terms
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Get slug
   *
   * @return string
   */
  public function getSlug()
  {
    return $this->slug;
  }

  /**
   * Set term
   *
   * @param string $term
   * @return Terms
   */
  public function setTerm($term)
  {
    $this->term = $term;

    return $this;
  }

  /**
   * Get term
   *
   * @return string
   */
  public function getTerm()
  {
    return $this->term;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return Terms
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
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Terms
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
   * @return Terms
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
