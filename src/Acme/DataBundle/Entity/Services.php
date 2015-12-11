<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Services
*/
class Services
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
  private $slug;

  /**
   * @var string
   */
  private $icon;

  /**
   * @var string
   */
  private $featuredImage;

  /**
   * @var string
   */
  private $headerText;

  /**
   * @var string
   */
  private $headerServiceText;

  /**
   * @var string
   */
  private $shortDescription;

  /**
   * @var string
   */
  private $longDescription;

  /**
   * @var string
   */
  private $longFZDescription;

  /**
   * @var string
   */
  private $bottomText;

  /**
   * @var boolean
   */
  private $isFeatured;

  /**
   * @var boolean
   */
  private $isMain;

  /**
   * @var boolean
   */
  private $isPrimary;

  /**
   * @var boolean
   */
  private $isAdditional;

  /**
   * @var boolean
   */
  private $isAmenity;

  /**
   * @var boolean
   */
  private $isFullSlate;

  /**
   * @var boolean
   */
  private $isCsv;

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
   * @var \Acme\DataBundle\Entity\Services
   */
  private $parent;


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
   * @return Services
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
   * Set slug
   *
   * @param string $slug
   * @return Services
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
   * Set icon
   *
   * @param string $icon
   * @return Services
   */
  public function setIcon($icon)
  {
    $this->icon = $icon;

    return $this;
  }

  /**
   * Get icon
   *
   * @return string
   */
  public function getIcon()
  {
    return $this->icon;
  }

  /**
   * Set featuredImage
   *
   * @param string $featuredImage
   * @return Services
   */
  public function setFeaturedImage($featuredImage)
  {
    $this->featuredImage = $featuredImage;

    return $this;
  }

  /**
   * Get featuredImage
   *
   * @return string
   */
  public function getFeaturedImage()
  {
    return $this->featuredImage;
  }

  /**
   * Set headerText
   *
   * @param string $headerText
   * @return Services
   */
  public function setHeaderText($headerText)
  {
    $this->headerText = $headerText;

    return $this;
  }

  /**
   * Get headerText
   *
   * @return string
   */
  public function getHeaderText()
  {
    return $this->headerText;
  }

  /**
   * Set headerServiceText
   *
   * @param string $headerServiceText
   * @return Services
   */
  public function setHeaderServiceText($headerServiceText)
  {
    $this->headerServiceText = $headerServiceText;

    return $this;
  }

  /**
   * Get headerServiceText
   *
   * @return string
   */
  public function getHeaderServiceText()
  {
    return $this->headerServiceText;
  }

  /**
   * Set shortDescription
   *
   * @param string $shortDescription
   * @return Services
   */
  public function setShortDescription($shortDescription)
  {
    $this->shortDescription = $shortDescription;

    return $this;
  }

  /**
   * Get shortDescription
   *
   * @return string
   */
  public function getShortDescription()
  {
    return $this->shortDescription;
  }

  /**
   * Set longDescription
   *
   * @param string $longDescription
   * @return Services
   */
  public function setLongDescription($longDescription)
  {
    $this->longDescription = $longDescription;

    return $this;
  }

  /**
   * Get longDescription
   *
   * @return string
   */
  public function getLongDescription()
  {
    return $this->longDescription;
  }

  /**
   * Set longFZDescription
   *
   * @param string $longFZDescription
   * @return Services
   */
  public function setLongFZDescription($longFZDescription)
  {
    $this->longFZDescription = $longFZDescription;

    return $this;
  }

  /**
   * Get longFZDescription
   *
   * @return string
   */
  public function getLongFZDescription()
  {
    return $this->longFZDescription;
  }

  /**
   * Set bottomText
   *
   * @param string $bottomText
   * @return Services
   */
  public function setBottomText($bottomText)
  {
    $this->bottomText = $bottomText;

    return $this;
  }

  /**
   * Get bottomText
   *
   * @return string
   */
  public function getBottomText()
  {
    return $this->bottomText;
  }

  /**
   * Set isFeatured
   *
   * @param boolean $isFeatured
   * @return Services
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
   * Set isMain
   *
   * @param boolean $isMain
   * @return Services
   */
  public function setIsMain($isMain)
  {
    $this->isMain = $isMain;

    return $this;
  }

  /**
   * Get isMain
   *
   * @return boolean
   */
  public function getIsMain()
  {
    return $this->isMain;
  }

  /**
   * Set isPrimary
   *
   * @param boolean $isPrimary
   * @return Services
   */
  public function setIsPrimary($isPrimary)
  {
    $this->isPrimary = $isPrimary;

    return $this;
  }

  /**
   * Get isPrimary
   *
   * @return boolean
   */
  public function getIsPrimary()
  {
    return $this->isPrimary;
  }

  /**
   * Set isAdditional
   *
   * @param boolean $isAdditional
   * @return Services
   */
  public function setIsAdditional($isAdditional)
  {
    $this->isAdditional = $isAdditional;

    return $this;
  }

  /**
   * Get isAdditional
   *
   * @return boolean
   */
  public function getIsAdditional()
  {
    return $this->isAdditional;
  }

  /**
   * Set isAmenity
   *
   * @param boolean $isAmenity
   * @return Services
   */
  public function setIsAmenity($isAmenity)
  {
    $this->isAmenity = $isAmenity;

    return $this;
  }

  /**
   * Get isAmenity
   *
   * @return boolean
   */
  public function getIsAmenity()
  {
    return $this->isAmenity;
  }

  /**
   * Set isFullSlate
   *
   * @param boolean $isFullSlate
   * @return Services
   */
  public function setIsFullSlate($isFullSlate)
  {
    $this->isFullSlate = $isFullSlate;

    return $this;
  }

  /**
   * Get isFullSlate
   *
   * @return boolean
   */
  public function getIsFullSlate()
  {
    return $this->isFullSlate;
  }

  /**
   * Set isCsv
   *
   * @param boolean $isCsv
   * @return Services
   */
  public function setIsCsv($isCsv)
  {
    $this->isCsv = $isCsv;

    return $this;
  }

  /**
   * Get isCsv
   *
   * @return boolean
   */
  public function getIsCsv()
  {
    return $this->isCsv;
  }

  /**
   * Set orderIdx
   *
   * @param integer $orderIdx
   * @return Services
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
   * @return Services
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
   * @return Services
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
   * Set parent
   *
   * @param \Acme\DataBundle\Entity\Services $parent
   * @return Services
   */
  public function setParent(\Acme\DataBundle\Entity\Services $parent = null)
  {
    $this->parent = $parent;

    return $this;
  }

  /**
   * Get parent
   *
   * @return \Acme\DataBundle\Entity\Services
   */
  public function getParent()
  {
    return $this->parent;
  }
}
