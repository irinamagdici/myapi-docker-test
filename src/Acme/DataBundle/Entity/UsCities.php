<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsCities
 */
class UsCities
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $zip;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $citySlug;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $lat;

    /**
     * @var string
     */
    private $lng;


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
     * Set zip
     *
     * @param string $zip
     * @return UsCities
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string 
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return UsCities
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
     * Set citySlug
     *
     * @param string $citySlug
     * @return UsCities
     */
    public function setCitySlug($citySlug)
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    /**
     * Get citySlug
     *
     * @return string 
     */
    public function getCitySlug()
    {
        return $this->citySlug;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return UsCities
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
     * Set lat
     *
     * @param string $lat
     * @return UsCities
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return string 
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param string $lng
     * @return UsCities
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return string 
     */
    public function getLng()
    {
        return $this->lng;
    }
}
