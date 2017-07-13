<?php

namespace AppBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
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
     *
     * @Groups({"public"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Groups({"public"})
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
     *
     * @Groups({"public"})
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="district", type="integer")
     *
     * @Groups({"public"})
     */
    private $district;

    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=255, nullable=true)
     *
     * @Groups({"public"})
     */
    private $web;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="web_crawled", type="datetime", nullable=true)
     *
     */
    private $webCrawled;

    /**
     * @var string
     *
     * @ORM\Column(name="web_content", type="text", nullable=true)
     */
    private $webContent;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string")
     *
     * @Groups({"public"})
     */
    private $category;

    /**
     * @var float
     *
     * @ORM\Column(name="uniqueness", type="float")
     */
    private $uniqueness;

    /**
     * array
     *
     * @ORM\Column(name="tags", type="array")
     */
    private $tags = [];

    /**
     * @var int
     *
     * @Groups({"public"})
     */
    public $relevance = 0;

    public function __construct($name = '', $address = '', $web = '', $id = null, $relevance = 0)
    {
        $this->webCrawled = new \DateTime('now');
        $this->name = $name;
        $this->address = $address;
        $this->web = $web;
        $this->id = $id;
        $this->relevance = $relevance;
    }

    /**
     * Set id
     *
     * @param int $id
     *
     * @return Museum
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set tags
     *
     * @param array $tags
     *
     * @return Museum
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set uniqueness
     *
     * @param float $uniqueness
     *
     * @return Museum
     */
    public function setUniqueness($uniqueness)
    {
        $this->uniqueness = $uniqueness;

        return $this;
    }

    /**
     * Get uniqueness
     *
     * @return float
     */
    public function getUniqueness()
    {
        return $this->uniqueness;
    }

    /**
     * @return \DateTime
     */
    public function getWebCrawled(): \DateTime
    {
        return $this->webCrawled;
    }

    /**
     * @param \DateTime $webCrawled
     */
    public function setWebCrawled(\DateTime $webCrawled)
    {
        $this->webCrawled = $webCrawled;
    }

    /**
     * @return string
     */
    public function getWebContent()
    {
        return $this->webContent;
    }

    /**
     * @param string $webContent
     */
    public function setWebContent($webContent)
    {
        $this->webContent = $webContent;
    }
}

