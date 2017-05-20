<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MuseumFeature
 *
 * @ORM\Table(name="museum_feature")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MuseumFeatureRepository")
 */
class MuseumFeature
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
     * @var float
     *
     * @ORM\Column(name="rating", type="float")
     */
    private $rating;

    /**
     * @var Museum
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Museum", inversedBy="features")
     */
    private $museum;

    /**
     * @var Feature
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Feature", inversedBy="libraries")
     */
    private $feature;

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
     * Set rating
     *
     * @param float $rating
     *
     * @return MuseumFeature
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return Museum
     */
    public function getMuseum()
    {
        return $this->museum;
    }

    /**
     * @param Museum $museum
     *
     * @return MuseumFeature
     */
    public function setMuseum($museum)
    {
        $this->museum = $museum;

        return $this;
    }

    /**
     * @return Feature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param Feature $feature
     *
     * @return MuseumFeature
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;

        return $this;
    }
}

