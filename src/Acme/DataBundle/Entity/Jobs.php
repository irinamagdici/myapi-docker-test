<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jobs
 */
class Jobs
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $code;

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
  private $jobType;

  /**
   * @var string
   */
  private $careerLevel;

  /**
   * @var string
   */
  private $education;

  /**
   * @var string
   */
  private $category;

  /**
   * @var string
   */
  private $requirements;

  /**
   * @var string
   */
  private $description;

  /**
   * @var string
   */
  private $quote;

  /**
   * @var string
   */
  private $authorQuote;

  /**
   * @var \DateTime
   */
  private $datePosted;

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
   * Set code
   *
   * @param string $code
   * @return Jobs
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Jobs
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
   * @return Jobs
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
   * Set jobType
   *
   * @param string $jobType
   * @return Jobs
   */
  public function setJobType($jobType)
  {
    $this->jobType = $jobType;

    return $this;
  }

  /**
   * Get jobType
   *
   * @return string
   */
  public function getJobType()
  {
    return $this->jobType;
  }

  /**
   * Set careerLevel
   *
   * @param string $careerLevel
   * @return Jobs
   */
  public function setCareerLevel($careerLevel)
  {
    $this->careerLevel = $careerLevel;

    return $this;
  }

  /**
   * Get careerLevel
   *
   * @return string
   */
  public function getCareerLevel()
  {
    return $this->careerLevel;
  }

  /**
   * Set education
   *
   * @param string $education
   * @return Jobs
   */
  public function setEducation($education)
  {
    $this->education = $education;

    return $this;
  }

  /**
   * Get education
   *
   * @return string
   */
  public function getEducation()
  {
    return $this->education;
  }

  /**
   * Set category
   *
   * @param string $category
   * @return Jobs
   */
  public function setCategory($category)
  {
    $this->category = $category;

    return $this;
  }

  /**
   * Get category
   *
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }

  /**
   * Set requirements
   *
   * @param string $requirements
   * @return Jobs
   */
  public function setRequirements($requirements)
  {
    $this->requirements = $requirements;

    return $this;
  }

  /**
   * Get requirements
   *
   * @return string
   */
  public function getRequirements()
  {
    return $this->requirements;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return Jobs
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Set quote
   *
   * @param string $quote
   * @return Jobs
   */
  public function setQuote($quote)
  {
    $this->quote = $quote;

    return $this;
  }

  /**
   * Get quote
   *
   * @return string
   */
  public function getQuote()
  {
    return $this->quote;
  }

  /**
   * Set authorQuote
   *
   * @param string $authorQuote
   * @return Jobs
   */
  public function setAuthorQuote($authorQuote)
  {
    $this->authorQuote = $authorQuote;

    return $this;
  }

  /**
   * Get authorQuote
   *
   * @return string
   */
  public function getAuthorQuote()
  {
    return $this->authorQuote;
  }

  /**
   * Set datePosted
   *
   * @param \DateTime $datePosted
   * @return Jobs
   */
  public function setDatePosted($datePosted)
  {
    $this->datePosted = $datePosted;

    return $this;
  }

  /**
   * Get datePosted
   *
   * @return \DateTime
   */
  public function getDatePosted()
  {
    return $this->datePosted;
  }

  /**
   * Set dateUpdated
   *
   * @param \DateTime $dateUpdated
   * @return Jobs
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
   * @return Jobs
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
