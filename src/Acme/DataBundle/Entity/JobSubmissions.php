<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* JobSubmissions
*/
class JobSubmissions
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $location;

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
  private $resumePdf;

  /**
   * @var string
   */
  private $body;

  /**
   * @var string
   */
  private $email;

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
   * @var \Acme\DataBundle\Entity\StoresHasJobs
   */
  private $storesHasJobs;


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
   * Set location
   *
   * @param string $location
   * @return JobSubmissions
   */
  public function setLocation($location)
  {
    $this->location = $location;

    return $this;
  }

  /**
   * Get location
   *
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }

  /**
   * Set firstName
   *
   * @param string $firstName
   * @return JobSubmissions
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
   * @return JobSubmissions
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
   * Set resumePdf
   *
   * @param string $resumePdf
   * @return JobSubmissions
   */
  public function setResumePdf($resumePdf)
  {
    $this->resumePdf = $resumePdf;

    return $this;
  }

  /**
   * Get resumePdf
   *
   * @return string
   */
  public function getResumePdf()
  {
    return $this->resumePdf;
  }

  /**
   * Set body
   *
   * @param string $body
   * @return JobSubmissions
   */
  public function setBody($body)
  {
    $this->body = $body;

    return $this;
  }

  /**
   * Get body
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Set email
   *
   * @param string $email
   * @return JobSubmissions
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
   * Set phone
   *
   * @param string $phone
   * @return JobSubmissions
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
   * @return JobSubmissions
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
   * @return JobSubmissions
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
   * Set storesHasJobs
   *
   * @param \Acme\DataBundle\Entity\StoresHasJobs $storesHasJobs
   * @return JobSubmissions
   */
  public function setStoresHasJobs(\Acme\DataBundle\Entity\StoresHasJobs $storesHasJobs = null)
  {
    $this->storesHasJobs = $storesHasJobs;

    return $this;
  }

  /**
   * Get storesHasJobs
   *
   * @return \Acme\DataBundle\Entity\StoresHasJobs
   */
  public function getStoresHasJobs()
  {
    return $this->storesHasJobs;
  }
}
