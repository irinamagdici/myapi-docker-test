<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Coupons
 */
class Coupons
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
  private $image;

  /**
   * @var string
   */
  private $barcode;

  /**
   * @var string
   */
  private $barcodeMail;

  /**
   * @var string
   */
  private $barcodeEmail;

  /**
   * @var string
   */
  private $offerType;

  /**
   * @var string
   */
  private $category;

  /**
   * @var boolean
   */
  private $status;

  /**
   * @var \DateTime
   */
  private $startDate;

  /**
   * @var \DateTime
   */
  private $endDate;

  /**
   * @var boolean
   */
  private $isDefault;

  /**
   * @var boolean
   */
  private $isLocked;

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
   * @return Coupons
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
   * Set image
   *
   * @param string $image
   * @return Coupons
   */
  public function setImage($image)
  {
    $this->image = $image;

    return $this;
  }

  /**
   * Get image
   *
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * Set barcode
   *
   * @param string $barcode
   * @return Coupons
   */
  public function setBarcode($barcode)
  {
    $this->barcode = $barcode;

    return $this;
  }

  /**
   * Get barcode
   *
   * @return string
   */
  public function getBarcode()
  {
    return $this->barcode;
  }

  /**
   * Set barcodeMail
   *
   * @param string $barcodeMail
   * @return Coupons
   */
  public function setBarcodeMail($barcodeMail)
  {
    $this->barcodeMail = $barcodeMail;

    return $this;
  }

  /**
   * Get barcodeMail
   *
   * @return string
   */
  public function getBarcodeMail()
  {
    return $this->barcodeMail;
  }

  /**
   * Set barcodeEmail
   *
   * @param string $barcodeEmail
   * @return Coupons
   */
  public function setBarcodeEmail($barcodeEmail)
  {
    $this->barcodeEmail = $barcodeEmail;

    return $this;
  }

  /**
   * Get barcodeEmail
   *
   * @return string
   */
  public function getBarcodeEmail()
  {
    return $this->barcodeEmail;
  }

  /**
   * Set offerType
   *
   * @param string $offerType
   * @return Coupons
   */
  public function setOfferType($offerType)
  {
    $this->offerType = $offerType;

    return $this;
  }

  /**
   * Get offerType
   *
   * @return string
   */
  public function getOfferType()
  {
    return $this->offerType;
  }

  /**
   * Set category
   *
   * @param string $category
   * @return Coupons
   */
  public function setCategory($category)
  {
    $this->category = $category;

    return $this;
  }

  /**
   * Get category
   *
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }

  /**
   * Set status
   *
   * @param boolean $status
   * @return Coupons
   */
  public function setStatus($status)
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return boolean
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Set startDate
   *
   * @param \DateTime $startDate
   * @return Coupons
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;

    return $this;
  }

  /**
   * Get startDate
   *
   * @return \DateTime
   */
  public function getStartDate()
  {
    return $this->startDate;
  }

  /**
   * Set endDate
   *
   * @param \DateTime $endDate
   * @return Coupons
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;

    return $this;
  }

  /**
   * Get endDate
   *
   * @return \DateTime
   */
  public function getEndDate()
  {
    return $this->endDate;
  }

  /**
   * Set isDefault
   *
   * @param boolean $isDefault
   * @return Coupons
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;

    return $this;
  }

  /**
   * Get isDefault
   *
   * @return boolean
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }

  /**
   * Set isLocked
   *
   * @param boolean $isLocked
   * @return Coupons
   */
  public function setIsLocked($isLocked)
  {
    $this->isLocked = $isLocked;

    return $this;
  }

  /**
   * Get isLocked
   *
   * @return boolean
   */
  public function getIsLocked()
  {
    return $this->isLocked;
  }

  /**
   * Set orderIdx
   *
   * @param integer $orderIdx
   * @return Coupons
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
   * @return Coupons
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
   * @return Coupons
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
