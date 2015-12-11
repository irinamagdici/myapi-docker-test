<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* StoresHasJobs
*/
class StoresHasJobs
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var \Acme\DataBundle\Entity\Stores
   */
  private $stores;

  /**
   * @var \Acme\DataBundle\Entity\Jobs
   */
  private $jobs;


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
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return StoresHasJobs
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
   * Set jobs
   *
   * @param \Acme\DataBundle\Entity\Jobs $jobs
   * @return StoresHasJobs
   */
  public function setJobs(\Acme\DataBundle\Entity\Jobs $jobs = null)
  {
    $this->jobs = $jobs;

    return $this;
  }

  /**
   * Get jobs
   *
   * @return \Acme\DataBundle\Entity\Jobs
   */
  public function getJobs()
  {
    return $this->jobs;
  }
}
