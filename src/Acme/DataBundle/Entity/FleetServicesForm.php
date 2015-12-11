<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FleetServicesForm
 */
class FleetServicesForm
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $organizationName;

    /**
     * @var string
     */
    private $contactFullName;

    /**
     * @var string
     */
    private $contactPhone;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $address;

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
    private $totalVehicles;

    /**
     * @var string
     */
    private $avgNumber;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var boolean
     */
    private $scheduleMaintenance;

    /**
     * @var boolean
     */
    private $purchaseOrderSystem;

    /**
     * @var boolean
     */
    private $centralizedBilling;

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
     * Set organizationName
     *
     * @param string $organizationName
     * @return FleetServicesForm
     */
    public function setOrganizationName($organizationName)
    {
        $this->organizationName = $organizationName;

        return $this;
    }

    /**
     * Get organizationName
     *
     * @return string 
     */
    public function getOrganizationName()
    {
        return $this->organizationName;
    }

    /**
     * Set contactFullName
     *
     * @param string $contactFullName
     * @return FleetServicesForm
     */
    public function setContactFullName($contactFullName)
    {
        $this->contactFullName = $contactFullName;

        return $this;
    }

    /**
     * Get contactFullName
     *
     * @return string 
     */
    public function getContactFullName()
    {
        return $this->contactFullName;
    }

    /**
     * Set contactPhone
     *
     * @param string $contactPhone
     * @return FleetServicesForm
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    /**
     * Get contactPhone
     *
     * @return string 
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return FleetServicesForm
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * Get contactEmail
     *
     * @return string 
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return FleetServicesForm
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return FleetServicesForm
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
     * @return FleetServicesForm
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
     * @return FleetServicesForm
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
     * Set totalVehicles
     *
     * @param string $totalVehicles
     * @return FleetServicesForm
     */
    public function setTotalVehicles($totalVehicles)
    {
        $this->totalVehicles = $totalVehicles;

        return $this;
    }

    /**
     * Get totalVehicles
     *
     * @return string 
     */
    public function getTotalVehicles()
    {
        return $this->totalVehicles;
    }

    /**
     * Set avgNumber
     *
     * @param string $avgNumber
     * @return FleetServicesForm
     */
    public function setAvgNumber($avgNumber)
    {
        $this->avgNumber = $avgNumber;

        return $this;
    }

    /**
     * Get avgNumber
     *
     * @return string 
     */
    public function getAvgNumber()
    {
        return $this->avgNumber;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return FleetServicesForm
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
     * Set scheduleMaintenance
     *
     * @param boolean $scheduleMaintenance
     * @return FleetServicesForm
     */
    public function setScheduleMaintenance($scheduleMaintenance)
    {
        $this->scheduleMaintenance = $scheduleMaintenance;

        return $this;
    }

    /**
     * Get scheduleMaintenance
     *
     * @return boolean 
     */
    public function getScheduleMaintenance()
    {
        return $this->scheduleMaintenance;
    }

    /**
     * Set purchaseOrderSystem
     *
     * @param boolean $purchaseOrderSystem
     * @return FleetServicesForm
     */
    public function setPurchaseOrderSystem($purchaseOrderSystem)
    {
        $this->purchaseOrderSystem = $purchaseOrderSystem;

        return $this;
    }

    /**
     * Get purchaseOrderSystem
     *
     * @return boolean 
     */
    public function getPurchaseOrderSystem()
    {
        return $this->purchaseOrderSystem;
    }

    /**
     * Set centralizedBilling
     *
     * @param boolean $centralizedBilling
     * @return FleetServicesForm
     */
    public function setCentralizedBilling($centralizedBilling)
    {
        $this->centralizedBilling = $centralizedBilling;

        return $this;
    }

    /**
     * Get centralizedBilling
     *
     * @return boolean 
     */
    public function getCentralizedBilling()
    {
        return $this->centralizedBilling;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return FleetServicesForm
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
     * @return FleetServicesForm
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
