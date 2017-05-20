<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Museum
 *
 * @ORM\Table(name="museum")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MuseumRepository")
 */
class Museum
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fid", type="string", length=255, unique=true)
     */
    private $fid;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="district", type="integer")
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var float
     *
     * @ORM\Column(name="prominence", type="float")
     */
    private $prominence;

    /**
     * @var ArrayCollection|Feature[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MuseumFeature", mappedBy="museum", cascade={"persist", "remove"})
     */
    private $features;

    public function __construct()
    {
        $this->libraries = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Museum
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
     * Set fid
     *
     * @param string $fid
     *
     * @return Museum
     */
    public function setFid($fid)
    {
        $this->fid = $fid;

        return $this;
    }

    /**
     * Get fid
     *
     * @return string
     */
    public function getFid()
    {
        return $this->fid;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Museum
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
     * Set district
     *
     * @param integer $district
     *
     * @return Museum
     */
    public function setDistrict($district)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return int
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Set web
     *
     * @param string $web
     *
     * @return Museum
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * Get web
     *
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Museum
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
     * Set prominence
     *
     * @param float $prominence
     *
     * @return Museum
     */
    public function setProminence($prominence)
    {
        $this->prominence = $prominence;

        return $this;
    }

    /**
     * Get prominence
     *
     * @return float
     */
    public function getProminence()
    {
        return $this->prominence;
    }

    /**
     * @return ArrayCollection|Feature[]
     */
    public function getFeatures()
    {
        return $this->features;
    }

    /**
     * @param ArrayCollection|Feature[] $features
     *
     * @return Museum
     */
    public function setFeatures($features)
    {
        $this->features = $features;

        return $this;
    }
}
