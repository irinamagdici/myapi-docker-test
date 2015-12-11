<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarCareClubForm
 */
class CarCareClubForm
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $firstName;

  /**
   * @var string
   */
  private $lastName;

  /**
   * @var string
   */
  private $email;

  /**
   * @var string
   */
  private $address1;

  /**
   * @var string
   */
  private $address2;

  /**
   * @var string
   */
  private $city;

  /**
   * @var string
   */
  private $state;

  /**
   * @var string
   */
  private $zipCode;

  /**
   * @var string
   */
  private $phone;

  /**
   * @var boolean
   */
  private $meinekeCustomer;

  /**
   * @var string
   */
  private $stateVisitMeineke;

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
   * Set firstName
   *
   * @param string $firstName
   * @return CarCareClubForm
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
   * @return CarCareClubForm
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
   * Set email
   *
   * @param string $email
   * @return CarCareClubForm
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
   * Set address1
   *
   * @param string $address1
   * @return CarCareClubForm
   */
  public function setAddress1($address1)
  {
    $this->address1 = $address1;

    return $this;
  }

  /**
   * Get address1
   *
   * @return string
   */
  public function getAddress1()
  {
    return $this->address1;
  }

  /**
   * Set address2
   *
   * @param string $address2
   * @return CarCareClubForm
   */
  public function setAddress2($address2)
  {
    $this->address2 = $address2;

    return $this;
  }

  /**
   * Get address2
   *
   * @return string
   */
  public function getAddress2()
  {
    return $this->address2;
  }

  /**
   * Set city
   *
   * @param string $city
   * @return CarCareClubForm
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
   * @return CarCareClubForm
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
   * Set zipCode
   *
   * @param string $zipCode
   * @return CarCareClubForm
   */
  public function setZipCode($zipCode)
  {
    $this->zipCode = $zipCode;

    return $this;
  }

  /**
   * Get zipCode
   *
   * @return string
   */
  public function getZipCode()
  {
    return $this->zipCode;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @return CarCareClubForm
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
   * Set meinekeCustomer
   *
   * @param boolean $meinekeCustomer
   * @return CarCareClubForm
   */
  public function setMeinekeCustomer($meinekeCustomer)
  {
    $this->meinekeCustomer = $meinekeCustomer;

    return $this;
  }

  /**
   * Get meinekeCustomer
   *
   * @return boolean
   */
  public function getMeinekeCustomer()
  {
    return $this->meinekeCustomer;
  }

  /**
   * Set stateVisitMeineke
   *
   * @param string $stateVisitMeineke
   * @return CarCareClubForm
   */
  public function setStateVisitMeineke($stateVisitMeineke)
  {
    $this->stateVisitMeineke = $stateVisitMeineke;

    return $this;
  }

  /**
   * Get stateVisitMeineke
   *
   * @return string
   */
  public function getStateVisitMeineke()
  {
    return $this->stateVisitMeineke;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return CarCareClubForm
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
   * @return CarCareClubForm
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
