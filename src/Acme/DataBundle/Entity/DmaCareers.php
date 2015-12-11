<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DmaCareers
 */
class DmaCareers
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $dma;

    /**
     * @var string
     */
    private $state;

    /**
     * @var string
     */
    private $dmaId;


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
     * Set dma
     *
     * @param string $dma
     * @return DmaCareers
     */
    public function setDma($dma)
    {
        $this->dma = $dma;

        return $this;
    }

    /**
     * Get dma
     *
     * @return string 
     */
    public function getDma()
    {
        return $this->dma;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return DmaCareers
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
     * Set dmaId
     *
     * @param string $dmaId
     * @return DmaCareers
     */
    public function setDmaId($dmaId)
    {
        $this->dmaId = $dmaId;

        return $this;
    }

    /**
     * Get dmaId
     *
     * @return string 
     */
    public function getDmaId()
    {
        return $this->dmaId;
    }
}
