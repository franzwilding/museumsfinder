<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Feature
 *
 * @ORM\Table(name="feature")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeatureRepository")
 */
class Feature
{
    CONST FEATURE_FREIER_EINTRITT = 'Freier Eintritt';
    const FEATURE_BARRIEREFREI = 'Barrierefrei';
    const FEATURE_ENGLISCH = 'Englisch';

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection|Museum[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\MuseumFeature", mappedBy="feature", cascade={"persist", "remove"})
     */
    private $museums;

    public function __construct()
    {
        $this->museums = new ArrayCollection();
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
     * @return Feature
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
     * @return ArrayCollection|Museum[]
     */
    public function getMuseums()
    {
        return $this->museums;
    }

    /**
     * @param ArrayCollection|Museum[] $museums
     *
     * @return Feature
     */
    public function setMuseums($museums)
    {
        $this->museums = $museums;

        return $this;
    }
}

