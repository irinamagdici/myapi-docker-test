<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RealEstateForm
 */
class RealEstateForm
{
    /**
     * @var integer
     */
    private $id;

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
    private $country;

    /**
     * @var \DateTime
     */
    private $dateAvailable;

    /**
     * @var string
     */
    private $dealType;

    /**
     * @var string
     */
    private $buildingSize;

    /**
     * @var string
     */
    private $buildingDepth;

    /**
     * @var string
     */
    private $salePrice;

    /**
     * @var string
     */
    private $landSizeSqFt;

    /**
     * @var string
     */
    private $zonedAuto;

    /**
     * @var string
     */
    private $buildingLength;

    /**
     * @var string
     */
    private $landSize;

    /**
     * @var string
     */
    private $leaseRate;

    /**
     * @var string
     */
    private $propertyTaxes;

    /**
     * @var string
     */
    private $contactFirstName;

    /**
     * @var string
     */
    private $contactLastName;

    /**
     * @var string
     */
    private $contactAddress;

    /**
     * @var string
     */
    private $contactEmail;

    /**
     * @var string
     */
    private $contactPhone;

    /**
     * @var string
     */
    private $comments;


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
     * Set address
     *
     * @param string $address
     * @return RealEstateForm
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
     * @return RealEstateForm
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
     * @return RealEstateForm
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
     * Set country
     *
     * @param string $country
     * @return RealEstateForm
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set dateAvailable
     *
     * @param \DateTime $dateAvailable
     * @return RealEstateForm
     */
    public function setDateAvailable($dateAvailable)
    {
        $this->dateAvailable = $dateAvailable;

        return $this;
    }

    /**
     * Get dateAvailable
     *
     * @return \DateTime 
     */
    public function getDateAvailable()
    {
        return $this->dateAvailable;
    }

    /**
     * Set dealType
     *
     * @param string $dealType
     * @return RealEstateForm
     */
    public function setDealType($dealType)
    {
        $this->dealType = $dealType;

        return $this;
    }

    /**
     * Get dealType
     *
     * @return string 
     */
    public function getDealType()
    {
        return $this->dealType;
    }

    /**
     * Set buildingSize
     *
     * @param string $buildingSize
     * @return RealEstateForm
     */
    public function setBuildingSize($buildingSize)
    {
        $this->buildingSize = $buildingSize;

        return $this;
    }

    /**
     * Get buildingSize
     *
     * @return string 
     */
    public function getBuildingSize()
    {
        return $this->buildingSize;
    }

    /**
     * Set buildingDepth
     *
     * @param string $buildingDepth
     * @return RealEstateForm
     */
    public function setBuildingDepth($buildingDepth)
    {
        $this->buildingDepth = $buildingDepth;

        return $this;
    }

    /**
     * Get buildingDepth
     *
     * @return string 
     */
    public function getBuildingDepth()
    {
        return $this->buildingDepth;
    }

    /**
     * Set salePrice
     *
     * @param string $salePrice
     * @return RealEstateForm
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    /**
     * Get salePrice
     *
     * @return string 
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * Set landSizeSqFt
     *
     * @param string $landSizeSqFt
     * @return RealEstateForm
     */
    public function setLandSizeSqFt($landSizeSqFt)
    {
        $this->landSizeSqFt = $landSizeSqFt;

        return $this;
    }

    /**
     * Get landSizeSqFt
     *
     * @return string 
     */
    public function getLandSizeSqFt()
    {
        return $this->landSizeSqFt;
    }

    /**
     * Set zonedAuto
     *
     * @param string $zonedAuto
     * @return RealEstateForm
     */
    public function setZonedAuto($zonedAuto)
    {
        $this->zonedAuto = $zonedAuto;

        return $this;
    }

    /**
     * Get zonedAuto
     *
     * @return string 
     */
    public function getZonedAuto()
    {
        return $this->zonedAuto;
    }

    /**
     * Set buildingLength
     *
     * @param string $buildingLength
     * @return RealEstateForm
     */
    public function setBuildingLength($buildingLength)
    {
        $this->buildingLength = $buildingLength;

        return $this;
    }

    /**
     * Get buildingLength
     *
     * @return string 
     */
    public function getBuildingLength()
    {
        return $this->buildingLength;
    }

    /**
     * Set landSize
     *
     * @param string $landSize
     * @return RealEstateForm
     */
    public function setLandSize($landSize)
    {
        $this->landSize = $landSize;

        return $this;
    }

    /**
     * Get landSize
     *
     * @return string 
     */
    public function getLandSize()
    {
        return $this->landSize;
    }

    /**
     * Set leaseRate
     *
     * @param string $leaseRate
     * @return RealEstateForm
     */
    public function setLeaseRate($leaseRate)
    {
        $this->leaseRate = $leaseRate;

        return $this;
    }

    /**
     * Get leaseRate
     *
     * @return string 
     */
    public function getLeaseRate()
    {
        return $this->leaseRate;
    }

    /**
     * Set propertyTaxes
     *
     * @param string $propertyTaxes
     * @return RealEstateForm
     */
    public function setPropertyTaxes($propertyTaxes)
    {
        $this->propertyTaxes = $propertyTaxes;

        return $this;
    }

    /**
     * Get propertyTaxes
     *
     * @return string 
     */
    public function getPropertyTaxes()
    {
        return $this->propertyTaxes;
    }

    /**
     * Set contactFirstName
     *
     * @param string $contactFirstName
     * @return RealEstateForm
     */
    public function setContactFirstName($contactFirstName)
    {
        $this->contactFirstName = $contactFirstName;

        return $this;
    }

    /**
     * Get contactFirstName
     *
     * @return string 
     */
    public function getContactFirstName()
    {
        return $this->contactFirstName;
    }

    /**
     * Set contactLastName
     *
     * @param string $contactLastName
     * @return RealEstateForm
     */
    public function setContactLastName($contactLastName)
    {
        $this->contactLastName = $contactLastName;

        return $this;
    }

    /**
     * Get contactLastName
     *
     * @return string 
     */
    public function getContactLastName()
    {
        return $this->contactLastName;
    }

    /**
     * Set contactAddress
     *
     * @param string $contactAddress
     * @return RealEstateForm
     */
    public function setContactAddress($contactAddress)
    {
        $this->contactAddress = $contactAddress;

        return $this;
    }

    /**
     * Get contactAddress
     *
     * @return string 
     */
    public function getContactAddress()
    {
        return $this->contactAddress;
    }

    /**
     * Set contactEmail
     *
     * @param string $contactEmail
     * @return RealEstateForm
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
     * Set contactPhone
     *
     * @param string $contactPhone
     * @return RealEstateForm
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
     * Set comments
     *
     * @param string $comments
     * @return RealEstateForm
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
}
