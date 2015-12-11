<?php

namespace Acme\DataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* Slides
*/
class Slides
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $bannerImage;

  /**
   * @var string
   */
  private $title;

  /**
   * @var string
   */
  private $htmlText;

  /**
   * @var string
   */
  private $slideURL;

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
   * Set bannerImage
   *
   * @param string $bannerImage
   * @return Slides
   */
  public function setBannerImage($bannerImage)
  {
    $this->bannerImage = $bannerImage;

    return $this;
  }

  /**
   * Get bannerImage
   *
   * @return string
   */
  public function getBannerImage()
  {
    return $this->bannerImage;
  }

  /**
   * Set title
   *
   * @param string $title
   * @return Slides
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set htmlText
   *
   * @param string $htmlText
   * @return Slides
   */
  public function setHtmlText($htmlText)
  {
    $this->htmlText = $htmlText;

    return $this;
  }

  /**
   * Get htmlText
   *
   * @return string
   */
  public function getHtmlText()
  {
    return $this->htmlText;
  }

  /**
   * Set slideURL
   *
   * @param string $slideURL
   * @return Slides
   */
  public function setSlideURL($slideURL)
  {
    $this->slideURL = $slideURL;

    return $this;
  }

  /**
   * Get slideURL
   *
   * @return string
   */
  public function getSlideURL()
  {
    return $this->slideURL;
  }

  /**
   * Set stores
   *
   * @param \Acme\DataBundle\Entity\Stores $stores
   * @return Slides
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
