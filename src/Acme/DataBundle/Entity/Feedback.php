<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 */
class Feedback
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

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
    private $phone;

    /**
     * @var boolean
     */
    private $rating;

    /**
     * @var string
     */
    private $feedbackText;

    /**
     * @var \DateTime
     */
    private $dateUpdated;

    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \Acme\DataBundle\Entity\Stores
     */
    private $stores;


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
     * Set email
     *
     * @param string $email
     * @return Feedback
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
     * Set firstName
     *
     * @param string $firstName
     * @return Feedback
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
     * @return Feedback
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
     * @return Feedback
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
     * Set rating
     *
     * @param boolean $rating
     * @return Feedback
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return boolean
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set feedbackText
     *
     * @param string $feedbackText
     * @return Feedback
     */
    public function setFeedbackText($feedbackText)
    {
        $this->feedbackText = $feedbackText;

        return $this;
    }

    /**
     * Get feedbackText
     *
     * @return string
     */
    public function getFeedbackText()
    {
        return $this->feedbackText;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return Feedback
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
     * @return Feedback
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
     * Set stores
     *
     * @param \Acme\DataBundle\Entity\Stores $stores
     * @return Feedback
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
}
