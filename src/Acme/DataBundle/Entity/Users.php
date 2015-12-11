<?php

namespace Acme\DataBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;

/**
* Users
*/
class Users extends BaseUser
{
  /**
   * @var integer
   */
  protected $id;

  /**
   * @var string
   */
  protected $firstName;

  /**
   * @var string
   */
  protected $lastName;

  /**
   * @var string
   */
  protected $phone;

  /**
   * @var string
   */
  protected $facebookId;

  /**
   * @var string
   */
  protected $address;

  /**
   * @var string
   */
  protected $city;

  /**
   * @var string
   */
  protected $state;

  /**
   * @var string
   */
  protected $country;

  /**
   * @var string
   */
  protected $cardNumber;

  /**
   * @var string
   */
  protected $customCardNumber;

  /**
   * @var string
   */
  protected $loyaltyPointsBalance;

  /**
   * @var boolean
   */
  private $newsletter;

  /**
   * @var \DateTime
   */
  protected $dateUpdated;

  /**
   * @var \DateTime
   */
  protected $dateCreated;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  protected $myStore;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  protected $lastVisitedStore;


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
   * Set firstName
   *
   * @param string $firstName
   * @return Users
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;

    return $this;
  }

  /**
   * Get firstName
   *
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }

  /**
   * Set lastName
   *
   * @param string $lastName
   * @return Users
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;

    return $this;
  }

  /**
   * Get lastName
   *
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @return Users
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
   * Set facebookId
   *
   * @param string $facebookId
   * @return Users
   */
  public function setFacebookId($facebookId)
  {
    $this->facebookId = $facebookId;

    return $this;
  }

  /**
   * Get facebookId
   *
   * @return string
   */
  public function getFacebookId()
  {
    return $this->facebookId;
  }

  /**
   * Set address
   *
   * @param string $address
   * @return Users
   */
  public function setAddress($address)
  {
    $this->address = $address;

    return $this;
  }

  /**
   * Get address
   *
   * @return string
   */
  public function getAddress()
  {
    return $this->address;
  }

  /**
   * Set city
   *
   * @param string $city
   * @return Users
   */
  public function setCity($city)
  {
    $this->city = $city;

    return $this;
  }

  /**
   * Get city
   *
   * @return string
   */
  public function getCity()
  {
    return $this->city;
  }

  /**
   * Set state
   *
   * @param string $state
   * @return Users
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
   * Set country
   *
   * @param string $country
   * @return Users
   */
  public function setCountry($country)
  {
    $this->country = $country;

    return $this;
  }

  /**
   * Get country
   *
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }

  /**
   * Set cardNumber
   *
   * @param string $cardNumber
   * @return Users
   */
  public function setCardNumber($cardNumber)
  {
    $this->cardNumber = $cardNumber;

    return $this;
  }

  /**
   * Get cardNumber
   *
   * @return string
   */
  public function getCardNumber()
  {
    return $this->cardNumber;
  }

  /**
   * Set customCardNumber
   *
   * @param string $customCardNumber
   * @return Users
   */
  public function setCustomCardNumber($customCardNumber)
  {
    $this->customCardNumber = $customCardNumber;

    return $this;
  }

  /**
   * Get customCardNumber
   *
   * @return string
   */
  public function getCustomCardNumber()
  {
    return $this->customCardNumber;
  }

  /**
   * Set loyaltyPointsBalance
   *
   * @param string $loyaltyPointsBalance
   * @return Users
   */
  public function setLoyaltyPointsBalance($loyaltyPointsBalance)
  {
    $this->loyaltyPointsBalance = $loyaltyPointsBalance;

    return $this;
  }

  /**
   * Get loyaltyPointsBalance
   *
   * @return string
   */
  public function getLoyaltyPointsBalance()
  {
    return $this->loyaltyPointsBalance;
  }

  /**
   * Set newsletter
   *
   * @param boolean $newsletter
   * @return Users
   */
  public function setNewsletter($newsletter)
  {
    $this->newsletter = $newsletter;

    return $this;
  }

  /**
   * Get newsletter
   *
   * @return boolean
   */
  public function getNewsletter()
  {
    return $this->newsletter;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Users
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
   * @return Users
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
   * Set myStore
   *
   * @param \Acme\DataBundle\Entity\Stores $myStore
   * @return Users
   */
  public function setMyStore(\Acme\DataBundle\Entity\Stores $myStore = null)
  {
    $this->myStore = $myStore;

    return $this;
  }

  /**
   * Get myStore
   *
   * @return \Acme\DataBundle\Entity\Stores
   */
  public function getMyStore()
  {
    return $this->myStore;
  }

  /**
   * Set lastVisitedStore
   *
   * @param \Acme\DataBundle\Entity\Stores $lastVisitedStore
   * @return Users
   */
  public function setLastVisitedStore(\Acme\DataBundle\Entity\Stores $lastVisitedStore = null)
  {
    $this->lastVisitedStore = $lastVisitedStore;

    return $this;
  }

  /**
   * Get lastVisitedStore
   *
   * @return \Acme\DataBundle\Entity\Stores
   */
  public function getLastVisitedStore()
  {
    return $this->lastVisitedStore;
  }
}
