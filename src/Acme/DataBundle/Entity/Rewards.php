<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rewards
 */
class Rewards
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
     * @var integer
     */
    private $points;

    /**
     * @var string
     */
    private $image;

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
     * Set name
     *
     * @param string $name
     * @return Rewards
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
     * Set points
     *
     * @param integer $points
     * @return Rewards
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Rewards
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set services
     *
     * @param \Acme\DataBundle\Entity\Services $services
     * @return Rewards
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
