<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DmaHasCoupons
 */
class DmaHasCoupons
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $orderIdx;

    /**
     * @var \Acme\DataBundle\Entity\Dma
     */
    private $dma;

    /**
     * @var \Acme\DataBundle\Entity\Coupons
     */
    private $coupons;


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
     * Set orderIdx
     *
     * @param integer $orderIdx
     * @return DmaHasCoupons
     */
    public function setOrderIdx($orderIdx)
    {
        $this->orderIdx = $orderIdx;

        return $this;
    }

    /**
     * Get orderIdx
     *
     * @return integer
     */
    public function getOrderIdx()
    {
        return $this->orderIdx;
    }

    /**
     * Set dma
     *
     * @param \Acme\DataBundle\Entity\Dma $dma
     * @return DmaHasCoupons
     */
    public function setDma(\Acme\DataBundle\Entity\Dma $dma = null)
    {
        $this->dma = $dma;

        return $this;
    }

    /**
     * Get dma
     *
     * @return \Acme\DataBundle\Entity\Dma
     */
    public function getDma()
    {
        return $this->dma;
    }

    /**
     * Set coupons
     *
     * @param \Acme\DataBundle\Entity\Coupons $coupons
     * @return DmaHasCoupons
     */
    public function setCoupons(\Acme\DataBundle\Entity\Coupons $coupons = null)
    {
        $this->coupons = $coupons;

        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Acme\DataBundle\Entity\Coupons
     */
    public function getCoupons()
    {
        return $this->coupons;
    }
}
