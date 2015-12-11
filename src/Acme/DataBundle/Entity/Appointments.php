<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Appointments
*/
class Appointments
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $fullSlateId;

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
  private $vehicleMake;

  /**
   * @var string
   */
  private $vehicleModel;

  /**
   * @var string
   */
  private $vehicleYear;

  /**
   * @var \DateTime
   */
  private $appointmentDate;

  /**
   * @var string
   */
  private $comments;

  /**
   * @var boolean
   */
  private $vehicleDropoff;

  /**
   * @var boolean
   */
  private $waitForCar;

  /**
   * @var boolean
   */
  private $textReminderSMS;

  /**
   * @var string
   */
  private $phone;

  /**
   * @var \DateTime
   */
  private $dateUpdated;

  /**
   * @var \DateTime
   */
  private $dateCreated;

  /**
   * @var \Acme\DataBundle\Entity\Users
   */
  private $users;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  private $stores;

  /**
   * @var \Acme\DataBundle\Entity\Vehicles
   */
  private $vehicles;


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
   * Set fullSlateId
   *
   * @param string $fullSlateId
   * @return Appointments
   */
  public function setFullSlateId($fullSlateId)
  {
    $this->fullSlateId = $fullSlateId;

    return $this;
  }

  /**
   * Get fullSlateId
   *
   * @return string
   */
  public function getFullSlateId()
  {
    return $this->fullSlateId;
  }

  /**
   * Set firstName
   *
   * @param string $firstName
   * @return Appointments
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
   * @return Appointments
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
   * @return Appointments
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
   * Set vehicleMake
   *
   * @param string $vehicleMake
   * @return Appointments
   */
  public function setVehicleMake($vehicleMake)
  {
    $this->vehicleMake = $vehicleMake;

    return $this;
  }

  /**
   * Get vehicleMake
   *
   * @return string
   */
  public function getVehicleMake()
  {
    return $this->vehicleMake;
  }

  /**
   * Set vehicleModel
   *
   * @param string $vehicleModel
   * @return Appointments
   */
  public function setVehicleModel($vehicleModel)
  {
    $this->vehicleModel = $vehicleModel;

    return $this;
  }

  /**
   * Get vehicleModel
   *
   * @return string
   */
  public function getVehicleModel()
  {
    return $this->vehicleModel;
  }

  /**
   * Set vehicleYear
   *
   * @param string $vehicleYear
   * @return Appointments
   */
  public function setVehicleYear($vehicleYear)
  {
    $this->vehicleYear = $vehicleYear;

    return $this;
  }

  /**
   * Get vehicleYear
   *
   * @return string
   */
  public function getVehicleYear()
  {
    return $this->vehicleYear;
  }

  /**
   * Set appointmentDate
   *
   * @param \DateTime $appointmentDate
   * @return Appointments
   */
  public function setAppointmentDate($appointmentDate)
  {
    $this->appointmentDate = $appointmentDate;

    return $this;
  }

  /**
   * Get appointmentDate
   *
   * @return \DateTime
   */
  public function getAppointmentDate()
  {
    return $this->appointmentDate;
  }

  /**
   * Set comments
   *
   * @param string $comments
   * @return Appointments
   */
  public function setComments($comments)
  {
    $this->comments = $comments;

    return $this;
  }

  /**
   * Get comments
   *
   * @return string
   */
  public function getComments()
  {
    return $this->comments;
  }

  /**
   * Set vehicleDropoff
   *
   * @param boolean $vehicleDropoff
   * @return Appointments
   */
  public function setVehicleDropoff($vehicleDropoff)
  {
    $this->vehicleDropoff = $vehicleDropoff;

    return $this;
  }

  /**
   * Get vehicleDropoff
   *
   * @return boolean
   */
  public function getVehicleDropoff()
  {
    return $this->vehicleDropoff;
  }

  /**
   * Set waitForCar
   *
   * @param boolean $waitForCar
   * @return Appointments
   */
  public function setWaitForCar($waitForCar)
  {
    $this->waitForCar = $waitForCar;

    return $this;
  }

  /**
   * Get waitForCar
   *
   * @return boolean
   */
  public function getWaitForCar()
  {
    return $this->waitForCar;
  }

  /**
   * Set textReminderSMS
   *
   * @param boolean $textReminderSMS
   * @return Appointments
   */
  public function setTextReminderSMS($textReminderSMS)
  {
    $this->textReminderSMS = $textReminderSMS;

    return $this;
  }

  /**
   * Get textReminderSMS
   *
   * @return boolean
   */
  public function getTextReminderSMS()
  {
    return $this->textReminderSMS;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @return Appointments
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
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Appointments
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
   * @return Appointments
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
   * Set users
   *
   * @param \Acme\DataBundle\Entity\Users $users
   * @return Appointments
   */
  public function setUsers(\Acme\DataBundle\Entity\Users $users = null)
  {
    $this->users = $users;

    return $this;
  }

  /**
   * Get users
   *
   * @return \Acme\DataBundle\Entity\Users
   */
  public function getUsers()
  {
    return $this->users;
  }

  /**
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return Appointments
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

  /**
   * Set vehicles
   *
   * @param \Acme\DataBundle\Entity\Vehicles $vehicles
   * @return Appointments
   */
  public function setVehicles(\Acme\DataBundle\Entity\Vehicles $vehicles = null)
  {
    $this->vehicles = $vehicles;

    return $this;
  }

  /**
   * Get vehicles
   *
   * @return \Acme\DataBundle\Entity\Vehicles
   */
  public function getVehicles()
  {
    return $this->vehicles;
  }
}
