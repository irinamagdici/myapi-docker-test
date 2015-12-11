<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarCareClubVehicles
 */
class CarCareClubVehicles
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $vehicleYear;

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
  private $vehicleMileage;

  /**
   * @var \Acme\DataBundle\Entity\CarCareClubForm
   */
  private $carCareClubForm;


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
   * Set vehicleYear
   *
   * @param string $vehicleYear
   * @return CarCareClubVehicles
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
   * Set vehicleMake
   *
   * @param string $vehicleMake
   * @return CarCareClubVehicles
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
   * @return CarCareClubVehicles
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
   * Set vehicleMileage
   *
   * @param string $vehicleMileage
   * @return CarCareClubVehicles
   */
  public function setVehicleMileage($vehicleMileage)
  {
    $this->vehicleMileage = $vehicleMileage;

    return $this;
  }

  /**
   * Get vehicleMileage
   *
   * @return string
   */
  public function getVehicleMileage()
  {
    return $this->vehicleMileage;
  }

  /**
   * Set carCareClubForm
   *
   * @param \Acme\DataBundle\Entity\CarCareClubForm $carCareClubForm
   * @return CarCareClubVehicles
   */
  public function setCarCareClubForm(\Acme\DataBundle\Entity\CarCareClubForm $carCareClubForm = null)
  {
    $this->carCareClubForm = $carCareClubForm;

    return $this;
  }

  /**
   * Get carCareClubForm
   *
   * @return \Acme\DataBundle\Entity\CarCareClubForm
   */
  public function getCarCareClubForm()
  {
    return $this->carCareClubForm;
  }
}
