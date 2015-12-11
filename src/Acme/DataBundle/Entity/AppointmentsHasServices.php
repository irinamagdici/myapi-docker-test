<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppointmentsHasServices
 */
class AppointmentsHasServices
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var \Acme\DataBundle\Entity\Appointments
   */
  private $appointments;

  /**
   * @var \Acme\DataBundle\Entity\Services
   */
  private $services;


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
   * Set appointments
   *
   * @param \Acme\DataBundle\Entity\Appointments $appointments
   * @return AppointmentsHasServices
   */
  public function setAppointments(\Acme\DataBundle\Entity\Appointments $appointments = null)
  {
    $this->appointments = $appointments;

    return $this;
  }

  /**
   * Get appointments
   *
   * @return \Acme\DataBundle\Entity\Appointments
   */
  public function getAppointments()
  {
    return $this->appointments;
  }

  /**
   * Set services
   *
   * @param \Acme\DataBundle\Entity\Services $services
   * @return AppointmentsHasServices
   */
  public function setServices(\Acme\DataBundle\Entity\Services $services = null)
  {
    $this->services = $services;

    return $this;
  }

  /**
   * Get services
   *
   * @return \Acme\DataBundle\Entity\Services
   */
  public function getServices()
  {
    return $this->services;
  }
}
