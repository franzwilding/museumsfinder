<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedbackRepository")
 */
class Feedback
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
     * @var int
     *
     * @ORM\Column(name="rating", type="integer")
     */
    private $rating;

    /**
     * @var Museum
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Museum", inversedBy="feedback")
     */
    private $museum;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="array")
     */
    private $parameters;

    public function __construct()
    {
        $this->parameters = [];
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
     * Set museum
     *
     * @param Museum $museum
     *
     * @return Feedback
     */
    public function setMuseum($museum)
    {
        $this->museum = $museum;

        return $this;
    }

    /**
     * Get museum
     *
     * @return Museum
     */
    public function getMuseum()
    {
        return $this->museum;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Feedback
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return Feedback
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

