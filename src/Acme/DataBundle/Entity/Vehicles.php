<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Vehicles
*/
class Vehicles
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $make;

  /**
   * @var string
   */
  private $model;

  /**
   * @var string
   */
  private $year;

  /**
   * @var string
   */
  private $licensePlate;

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
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set make
   *
   * @param string $make
   * @return Vehicles
   */
  public function setMake($make)
  {
    $this->make = $make;

    return $this;
  }

  /**
   * Get make
   *
   * @return string
   */
  public function getMake()
  {
    return $this->make;
  }

  /**
   * Set model
   *
   * @param string $model
   * @return Vehicles
   */
  public function setModel($model)
  {
    $this->model = $model;

    return $this;
  }

  /**
   * Get model
   *
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   * Set year
   *
   * @param string $year
   * @return Vehicles
   */
  public function setYear($year)
  {
    $this->year = $year;

    return $this;
  }

  /**
   * Get year
   *
   * @return string
   */
  public function getYear()
  {
    return $this->year;
  }

  /**
   * Set licensePlate
   *
   * @param string $licensePlate
   * @return Vehicles
   */
  public function setLicensePlate($licensePlate)
  {
    $this->licensePlate = $licensePlate;

    return $this;
  }

  /**
   * Get licensePlate
   *
   * @return string
   */
  public function getLicensePlate()
  {
    return $this->licensePlate;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Vehicles
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
   * @return Vehicles
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
   * @return Vehicles
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
}
