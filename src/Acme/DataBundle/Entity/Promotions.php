<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Promotions
*/
class Promotions
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $slug;

  /**
   * @var string
   */
  private $facebookTitle;

  /**
   * @var string
   */
  private $facebookText;

  /**
   * @var string
   */
  private $facebookImage;

  /**
   * @var string
   */
  private $twitterText;

  /**
   * @var string
   */
  private $emailBody;

  /**
   * @var string
   */
  private $emailSubject;

  /**
   * @var boolean
   */
  private $status;

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
   * Set name
   *
   * @param string $name
   * @return Promotions
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set slug
   *
   * @param string $slug
   * @return Promotions
   */
  public function setSlug($slug)
  {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Get slug
   *
   * @return string
   */
  public function getSlug()
  {
    return $this->slug;
  }

  /**
   * Set facebookTitle
   *
   * @param string $facebookTitle
   * @return Promotions
   */
  public function setFacebookTitle($facebookTitle)
  {
    $this->facebookTitle = $facebookTitle;

    return $this;
  }

  /**
   * Get facebookTitle
   *
   * @return string
   */
  public function getFacebookTitle()
  {
    return $this->facebookTitle;
  }

  /**
   * Set facebookText
   *
   * @param string $facebookText
   * @return Promotions
   */
  public function setFacebookText($facebookText)
  {
    $this->facebookText = $facebookText;

    return $this;
  }

  /**
   * Get facebookText
   *
   * @return string
   */
  public function getFacebookText()
  {
    return $this->facebookText;
  }

  /**
   * Set facebookImage
   *
   * @param string $facebookImage
   * @return Promotions
   */
  public function setFacebookImage($facebookImage)
  {
    $this->facebookImage = $facebookImage;

    return $this;
  }

  /**
   * Get facebookImage
   *
   * @return string
   */
  public function getFacebookImage()
  {
    return $this->facebookImage;
  }

  /**
   * Set twitterText
   *
   * @param string $twitterText
   * @return Promotions
   */
  public function setTwitterText($twitterText)
  {
    $this->twitterText = $twitterText;

    return $this;
  }

  /**
   * Get twitterText
   *
   * @return string
   */
  public function getTwitterText()
  {
    return $this->twitterText;
  }

  /**
   * Set emailBody
   *
   * @param string $emailBody
   * @return Promotions
   */
  public function setEmailBody($emailBody)
  {
    $this->emailBody = $emailBody;

    return $this;
  }

  /**
   * Get emailBody
   *
   * @return string
   */
  public function getEmailBody()
  {
    return $this->emailBody;
  }

  /**
   * Set emailSubject
   *
   * @param string $emailSubject
   * @return Promotions
   */
  public function setEmailSubject($emailSubject)
  {
    $this->emailSubject = $emailSubject;

    return $this;
  }

  /**
   * Get emailSubject
   *
   * @return string
   */
  public function getEmailSubject()
  {
    return $this->emailSubject;
  }

  /**
   * Set status
   *
   * @param boolean $status
   * @return Promotions
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
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Promotions
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
   * @return Promotions
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
