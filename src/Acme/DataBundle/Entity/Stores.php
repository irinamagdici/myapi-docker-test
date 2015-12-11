<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Stores
*/
class Stores
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var integer
   */
  private $storeId;

  /**
   * @var string
   */
  private $streetAddress1;

  /**
   * @var string
   */
  private $streetAddress2;

  /**
   * @var string
   */
  private $locationCity;

  /**
   * @var string
   */
  private $locationState;

  /**
   * @var string
   */
  private $locationPostalCode;

  /**
   * @var string
   */
  private $locationRegion;

  /**
   * @var string
   */
  private $locationCountry;

  /**
   * @var string
   */
  private $locationEmail;

  /**
   * @var string
   */
  private $locationStatus;

  /**
   * @var string
   */
  private $phone;

  /**
   * @var string
   */
  private $rawPhone;

  /**
   * @var string
   */
  private $semCamPhone;

  /**
   * @var string
   */
  private $rawSemCamPhone;

  /**
   * @var string
   */
  private $trackingPhone;

  /**
   * @var string
   */
  private $rawTrackingPhone;

  /**
   * @var string
   */
  private $lng;

  /**
   * @var string
   */
  private $lat;

  /**
   * @var string
   */
  private $storeURL;

  /**
   * @var string
   */
  private $facebookURL;

  /**
   * @var string
   */
  private $googleplusURL;

  /**
   * @var string
   */
  private $yelpURL;

  /**
   * @var string
   */
  private $foursquareURL;

  /**
   * @var string
   */
  private $primaryContact;

  /**
   * @var string
   */
  private $hoursWeekdayOpen;

  /**
   * @var string
   */
  private $hoursWeekdayClose;

  /**
   * @var string
   */
  private $hoursSaturdayOpen;

  /**
   * @var string
   */
  private $hoursSaturdayClose;

  /**
   * @var string
   */
  private $hoursSundayOpen;

  /**
   * @var string
   */
  private $hoursSundayClose;

  /**
   * @var string
   */
  private $locationDirections;

  /**
   * @var integer
   */
  private $starRating;

  /**
   * @var \DateTime
   */
  private $openDate;

  /**
   * @var boolean
   */
  private $americanExpress;

  /**
   * @var boolean
   */
  private $visa;

  /**
   * @var boolean
   */
  private $aseSymbol;

  /**
   * @var boolean
   */
  private $dinersClub;

  /**
   * @var boolean
   */
  private $discover;

  /**
   * @var boolean
   */
  private $mastercard;

  /**
   * @var boolean
   */
  private $meinekeCreditCard;

  /**
   * @var boolean
   */
  private $militaryDiscount;

  /**
   * @var boolean
   */
  private $seniorDiscount;

  /**
   * @var string
   */
  private $timezone;

  /**
   * @var boolean
   */
  private $customerBadge;

  /**
   * @var string
   */
  private $smallLabel;

  /**
   * @var string
   */
  private $bannerImage;

  /**
   * @var boolean
   */
  private $isFeatured;

  /**
   * @var integer
   */
  private $orderFeaturedIdx;

  /**
   * @var boolean
   */
  private $hasFullSlate;

  /**
   * @var boolean
   */
  private $optin;

  /**
   * @var boolean
   */
  private $hasVeterans;

  /**
   * @var \DateTime
   */
  private $dateUpdated;

  /**
   * @var \DateTime
   */
  private $dateCreated;

  /**
   * @var \Acme\DataBundle\Entity\Dma
   */
  private $dma;


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
   * Set storeId
   *
   * @param integer $storeId
   * @return Stores
   */
  public function setStoreId($storeId)
  {
    $this->storeId = $storeId;

    return $this;
  }

  /**
   * Get storeId
   *
   * @return integer
   */
  public function getStoreId()
  {
    return $this->storeId;
  }

  /**
   * Set streetAddress1
   *
   * @param string $streetAddress1
   * @return Stores
   */
  public function setStreetAddress1($streetAddress1)
  {
    $this->streetAddress1 = $streetAddress1;

    return $this;
  }

  /**
   * Get streetAddress1
   *
   * @return string
   */
  public function getStreetAddress1()
  {
    return $this->streetAddress1;
  }

  /**
   * Set streetAddress2
   *
   * @param string $streetAddress2
   * @return Stores
   */
  public function setStreetAddress2($streetAddress2)
  {
    $this->streetAddress2 = $streetAddress2;

    return $this;
  }

  /**
   * Get streetAddress2
   *
   * @return string
   */
  public function getStreetAddress2()
  {
    return $this->streetAddress2;
  }

  /**
   * Set locationCity
   *
   * @param string $locationCity
   * @return Stores
   */
  public function setLocationCity($locationCity)
  {
    $this->locationCity = $locationCity;

    return $this;
  }

  /**
   * Get locationCity
   *
   * @return string
   */
  public function getLocationCity()
  {
    return $this->locationCity;
  }

  /**
   * Set locationState
   *
   * @param string $locationState
   * @return Stores
   */
  public function setLocationState($locationState)
  {
    $this->locationState = $locationState;

    return $this;
  }

  /**
   * Get locationState
   *
   * @return string
   */
  public function getLocationState()
  {
    return $this->locationState;
  }

  /**
   * Set locationPostalCode
   *
   * @param string $locationPostalCode
   * @return Stores
   */
  public function setLocationPostalCode($locationPostalCode)
  {
    $this->locationPostalCode = $locationPostalCode;

    return $this;
  }

  /**
   * Get locationPostalCode
   *
   * @return string
   */
  public function getLocationPostalCode()
  {
    return $this->locationPostalCode;
  }

  /**
   * Set locationRegion
   *
   * @param string $locationRegion
   * @return Stores
   */
  public function setLocationRegion($locationRegion)
  {
    $this->locationRegion = $locationRegion;

    return $this;
  }

  /**
   * Get locationRegion
   *
   * @return string
   */
  public function getLocationRegion()
  {
    return $this->locationRegion;
  }

  /**
   * Set locationCountry
   *
   * @param string $locationCountry
   * @return Stores
   */
  public function setLocationCountry($locationCountry)
  {
    $this->locationCountry = $locationCountry;

    return $this;
  }

  /**
   * Get locationCountry
   *
   * @return string
   */
  public function getLocationCountry()
  {
    return $this->locationCountry;
  }

  /**
   * Set locationEmail
   *
   * @param string $locationEmail
   * @return Stores
   */
  public function setLocationEmail($locationEmail)
  {
    $this->locationEmail = $locationEmail;

    return $this;
  }

  /**
   * Get locationEmail
   *
   * @return string
   */
  public function getLocationEmail()
  {
    return $this->locationEmail;
  }

  /**
   * Set locationStatus
   *
   * @param string $locationStatus
   * @return Stores
   */
  public function setLocationStatus($locationStatus)
  {
    $this->locationStatus = $locationStatus;

    return $this;
  }

  /**
   * Get locationStatus
   *
   * @return string
   */
  public function getLocationStatus()
  {
    return $this->locationStatus;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @return Stores
   */
  public function setPhone($phone)
  {
    $this->phone = $phone;

    return $this;
  }

  /**
   * Get phone
   *
   * @return string
   */
  public function getPhone()
  {
    return $this->phone;
  }

  /**
   * Set rawPhone
   *
   * @param string $rawPhone
   * @return Stores
   */
  public function setRawPhone($rawPhone)
  {
    $this->rawPhone = $rawPhone;

    return $this;
  }

  /**
   * Get rawPhone
   *
   * @return string
   */
  public function getRawPhone()
  {
    return $this->rawPhone;
  }

  /**
   * Set semCamPhone
   *
   * @param string $semCamPhone
   * @return Stores
   */
  public function setSemCamPhone($semCamPhone)
  {
    $this->semCamPhone = $semCamPhone;

    return $this;
  }

  /**
   * Get semCamPhone
   *
   * @return string
   */
  public function getSemCamPhone()
  {
    return $this->semCamPhone;
  }

  /**
   * Set rawSemCamPhone
   *
   * @param string $rawSemCamPhone
   * @return Stores
   */
  public function setRawSemCamPhone($rawSemCamPhone)
  {
    $this->rawSemCamPhone = $rawSemCamPhone;

    return $this;
  }

  /**
   * Get rawSemCamPhone
   *
   * @return string
   */
  public function getRawSemCamPhone()
  {
    return $this->rawSemCamPhone;
  }

  /**
   * Set trackingPhone
   *
   * @param string $trackingPhone
   * @return Stores
   */
  public function setTrackingPhone($trackingPhone)
  {
    $this->trackingPhone = $trackingPhone;

    return $this;
  }

  /**
   * Get trackingPhone
   *
   * @return string
   */
  public function getTrackingPhone()
  {
    return $this->trackingPhone;
  }

  /**
   * Set rawTrackingPhone
   *
   * @param string $rawTrackingPhone
   * @return Stores
   */
  public function setRawTrackingPhone($rawTrackingPhone)
  {
    $this->rawTrackingPhone = $rawTrackingPhone;

    return $this;
  }

  /**
   * Get rawTrackingPhone
   *
   * @return string
   */
  public function getRawTrackingPhone()
  {
    return $this->rawTrackingPhone;
  }

  /**
   * Set lng
   *
   * @param string $lng
   * @return Stores
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
   * Set lat
   *
   * @param string $lat
   * @return Stores
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
   * Set storeURL
   *
   * @param string $storeURL
   * @return Stores
   */
  public function setStoreURL($storeURL)
  {
    $this->storeURL = $storeURL;

    return $this;
  }

  /**
   * Get storeURL
   *
   * @return string
   */
  public function getStoreURL()
  {
    return $this->storeURL;
  }

  /**
   * Set facebookURL
   *
   * @param string $facebookURL
   * @return Stores
   */
  public function setFacebookURL($facebookURL)
  {
    $this->facebookURL = $facebookURL;

    return $this;
  }

  /**
   * Get facebookURL
   *
   * @return string
   */
  public function getFacebookURL()
  {
    return $this->facebookURL;
  }

  /**
   * Set googleplusURL
   *
   * @param string $googleplusURL
   * @return Stores
   */
  public function setGoogleplusURL($googleplusURL)
  {
    $this->googleplusURL = $googleplusURL;

    return $this;
  }

  /**
   * Get googleplusURL
   *
   * @return string
   */
  public function getGoogleplusURL()
  {
    return $this->googleplusURL;
  }

  /**
   * Set yelpURL
   *
   * @param string $yelpURL
   * @return Stores
   */
  public function setYelpURL($yelpURL)
  {
    $this->yelpURL = $yelpURL;

    return $this;
  }

  /**
   * Get yelpURL
   *
   * @return string
   */
  public function getYelpURL()
  {
    return $this->yelpURL;
  }

  /**
   * Set foursquareURL
   *
   * @param string $foursquareURL
   * @return Stores
   */
  public function setFoursquareURL($foursquareURL)
  {
      $this->foursquareURL = $foursquareURL;

      return $this;
  }

  /**
   * Get foursquareURL
   *
   * @return string
   */
  public function getFoursquareURL()
  {
      return $this->foursquareURL;
  }

  /**
   * Set primaryContact
   *
   * @param string $primaryContact
   * @return Stores
   */
  public function setPrimaryContact($primaryContact)
  {
    $this->primaryContact = $primaryContact;

    return $this;
  }

  /**
   * Get primaryContact
   *
   * @return string
   */
  public function getPrimaryContact()
  {
    return $this->primaryContact;
  }

  /**
   * Set hoursWeekdayOpen
   *
   * @param string $hoursWeekdayOpen
   * @return Stores
   */
  public function setHoursWeekdayOpen($hoursWeekdayOpen)
  {
    $this->hoursWeekdayOpen = $hoursWeekdayOpen;

    return $this;
  }

  /**
   * Get hoursWeekdayOpen
   *
   * @return string
   */
  public function getHoursWeekdayOpen()
  {
    return $this->hoursWeekdayOpen;
  }

  /**
   * Set hoursWeekdayClose
   *
   * @param string $hoursWeekdayClose
   * @return Stores
   */
  public function setHoursWeekdayClose($hoursWeekdayClose)
  {
    $this->hoursWeekdayClose = $hoursWeekdayClose;

    return $this;
  }

  /**
   * Get hoursWeekdayClose
   *
   * @return string
   */
  public function getHoursWeekdayClose()
  {
    return $this->hoursWeekdayClose;
  }

  /**
   * Set hoursSaturdayOpen
   *
   * @param string $hoursSaturdayOpen
   * @return Stores
   */
  public function setHoursSaturdayOpen($hoursSaturdayOpen)
  {
    $this->hoursSaturdayOpen = $hoursSaturdayOpen;

    return $this;
  }

  /**
   * Get hoursSaturdayOpen
   *
   * @return string
   */
  public function getHoursSaturdayOpen()
  {
    return $this->hoursSaturdayOpen;
  }

  /**
   * Set hoursSaturdayClose
   *
   * @param string $hoursSaturdayClose
   * @return Stores
   */
  public function setHoursSaturdayClose($hoursSaturdayClose)
  {
    $this->hoursSaturdayClose = $hoursSaturdayClose;

    return $this;
  }

  /**
   * Get hoursSaturdayClose
   *
   * @return string
   */
  public function getHoursSaturdayClose()
  {
    return $this->hoursSaturdayClose;
  }

  /**
   * Set hoursSundayOpen
   *
   * @param string $hoursSundayOpen
   * @return Stores
   */
  public function setHoursSundayOpen($hoursSundayOpen)
  {
    $this->hoursSundayOpen = $hoursSundayOpen;

    return $this;
  }

  /**
   * Get hoursSundayOpen
   *
   * @return string
   */
  public function getHoursSundayOpen()
  {
    return $this->hoursSundayOpen;
  }

  /**
   * Set hoursSundayClose
   *
   * @param string $hoursSundayClose
   * @return Stores
   */
  public function setHoursSundayClose($hoursSundayClose)
  {
    $this->hoursSundayClose = $hoursSundayClose;

    return $this;
  }

  /**
   * Get hoursSundayClose
   *
   * @return string
   */
  public function getHoursSundayClose()
  {
    return $this->hoursSundayClose;
  }

  /**
   * Set locationDirections
   *
   * @param string $locationDirections
   * @return Stores
   */
  public function setLocationDirections($locationDirections)
  {
    $this->locationDirections = $locationDirections;

    return $this;
  }

  /**
   * Get locationDirections
   *
   * @return string
   */
  public function getLocationDirections()
  {
    return $this->locationDirections;
  }

  /**
   * Set starRating
   *
   * @param integer $starRating
   * @return Stores
   */
  public function setStarRating($starRating)
  {
    $this->starRating = $starRating;

    return $this;
  }

  /**
   * Get starRating
   *
   * @return integer
   */
  public function getStarRating()
  {
    return $this->starRating;
  }

  /**
   * Set openDate
   *
   * @param \DateTime $openDate
   * @return Stores
   */
  public function setOpenDate($openDate)
  {
    $this->openDate = $openDate;

    return $this;
  }

  /**
   * Get openDate
   *
   * @return \DateTime
   */
  public function getOpenDate()
  {
    return $this->openDate;
  }

  /**
   * Set americanExpress
   *
   * @param boolean $americanExpress
   * @return Stores
   */
  public function setAmericanExpress($americanExpress)
  {
    $this->americanExpress = $americanExpress;

    return $this;
  }

  /**
   * Get americanExpress
   *
   * @return boolean
   */
  public function getAmericanExpress()
  {
    return $this->americanExpress;
  }

  /**
   * Set visa
   *
   * @param boolean $visa
   * @return Stores
   */
  public function setVisa($visa)
  {
    $this->visa = $visa;

    return $this;
  }

  /**
   * Get visa
   *
   * @return boolean
   */
  public function getVisa()
  {
    return $this->visa;
  }

  /**
   * Set aseSymbol
   *
   * @param boolean $aseSymbol
   * @return Stores
   */
  public function setAseSymbol($aseSymbol)
  {
    $this->aseSymbol = $aseSymbol;

    return $this;
  }

  /**
   * Get aseSymbol
   *
   * @return boolean
   */
  public function getAseSymbol()
  {
    return $this->aseSymbol;
  }

  /**
   * Set dinersClub
   *
   * @param boolean $dinersClub
   * @return Stores
   */
  public function setDinersClub($dinersClub)
  {
    $this->dinersClub = $dinersClub;

    return $this;
  }

  /**
   * Get dinersClub
   *
   * @return boolean
   */
  public function getDinersClub()
  {
    return $this->dinersClub;
  }

  /**
   * Set discover
   *
   * @param boolean $discover
   * @return Stores
   */
  public function setDiscover($discover)
  {
    $this->discover = $discover;

    return $this;
  }

  /**
   * Get discover
   *
   * @return boolean
   */
  public function getDiscover()
  {
    return $this->discover;
  }

  /**
   * Set mastercard
   *
   * @param boolean $mastercard
   * @return Stores
   */
  public function setMastercard($mastercard)
  {
    $this->mastercard = $mastercard;

    return $this;
  }

  /**
   * Get mastercard
   *
   * @return boolean
   */
  public function getMastercard()
  {
    return $this->mastercard;
  }

  /**
   * Set meinekeCreditCard
   *
   * @param boolean $meinekeCreditCard
   * @return Stores
   */
  public function setMeinekeCreditCard($meinekeCreditCard)
  {
    $this->meinekeCreditCard = $meinekeCreditCard;

    return $this;
  }

  /**
   * Get meinekeCreditCard
   *
   * @return boolean
   */
  public function getMeinekeCreditCard()
  {
    return $this->meinekeCreditCard;
  }

  /**
   * Set militaryDiscount
   *
   * @param boolean $militaryDiscount
   * @return Stores
   */
  public function setMilitaryDiscount($militaryDiscount)
  {
    $this->militaryDiscount = $militaryDiscount;

    return $this;
  }

  /**
   * Get militaryDiscount
   *
   * @return boolean
   */
  public function getMilitaryDiscount()
  {
    return $this->militaryDiscount;
  }

  /**
   * Set seniorDiscount
   *
   * @param boolean $seniorDiscount
   * @return Stores
   */
  public function setSeniorDiscount($seniorDiscount)
  {
    $this->seniorDiscount = $seniorDiscount;

    return $this;
  }

  /**
   * Get seniorDiscount
   *
   * @return boolean
   */
  public function getSeniorDiscount()
  {
    return $this->seniorDiscount;
  }

  /**
   * Set timezone
   *
   * @param string $timezone
   * @return Stores
   */
  public function setTimezone($timezone)
  {
    $this->timezone = $timezone;

    return $this;
  }

  /**
   * Get timezone
   *
   * @return string
   */
  public function getTimezone()
  {
    return $this->timezone;
  }

  /**
   * Set customerBadge
   *
   * @param boolean $customerBadge
   * @return Stores
   */
  public function setCustomerBadge($customerBadge)
  {
    $this->customerBadge = $customerBadge;

    return $this;
  }

  /**
   * Get customerBadge
   *
   * @return boolean
   */
  public function getCustomerBadge()
  {
    return $this->customerBadge;
  }

  /**
   * Set smallLabel
   *
   * @param string $smallLabel
   * @return Stores
   */
  public function setSmallLabel($smallLabel)
  {
    $this->smallLabel = $smallLabel;

    return $this;
  }

  /**
   * Get smallLabel
   *
   * @return string
   */
  public function getSmallLabel()
  {
    return $this->smallLabel;
  }

  /**
   * Set bannerImage
   *
   * @param string $bannerImage
   * @return Stores
   */
  public function setBannerImage($bannerImage)
  {
    $this->bannerImage = $bannerImage;

    return $this;
  }

  /**
   * Get bannerImage
   *
   * @return string
   */
  public function getBannerImage()
  {
    return $this->bannerImage;
  }

  /**
   * Set isFeatured
   *
   * @param boolean $isFeatured
   * @return Stores
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
   * @return Stores
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
   * Set hasFullSlate
   *
   * @param boolean $hasFullSlate
   * @return Stores
   */
  public function setHasFullSlate($hasFullSlate)
  {
    $this->hasFullSlate = $hasFullSlate;

    return $this;
  }

  /**
   * Get hasFullSlate
   *
   * @return boolean
   */
  public function getHasFullSlate()
  {
    return $this->hasFullSlate;
  }

  /**
   * Set optin
   *
   * @param boolean $optin
   * @return Stores
   */
  public function setOptin($optin)
  {
    $this->optin = $optin;

    return $this;
  }

  /**
   * Get optin
   *
   * @return boolean
   */
  public function getOptin()
  {
    return $this->optin;
  }

  /**
   * Set hasVeterans
   *
   * @param boolean $hasVeterans
   * @return Stores
   */
  public function setHasVeterans($hasVeterans)
  {
    $this->hasVeterans = $hasVeterans;

    return $this;
  }

  /**
   * Get hasVeterans
   *
   * @return boolean
   */
  public function getHasVeterans()
  {
    return $this->hasVeterans;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Stores
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
   * @return Stores
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
   * Set dma
   *
   * @param \Acme\DataBundle\Entity\Dma $dma
   * @return Stores
   */
  public function setDma(\Acme\DataBundle\Entity\Dma $dma = null)
  {
    $this->dma = $dma;

    return $this;
  }

  /**
   * Get dma
   *
   * @return \Acme\DataBundle\Entity\Dma
   */
  public function getDma()
  {
    return $this->dma;
  }

}
