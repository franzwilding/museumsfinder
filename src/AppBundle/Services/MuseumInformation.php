<?php

namespace AppBundle\Services;

use AppBundle\Entity\Museum;
use AppBundle\Entity\MuseumFeature;
use GuzzleHttp\Client;

class MuseumInformation {

    /**
     * @var Client $webCrawlerClient
     */
    private $webCrawlerClient;

    /**
     * @var array
     */
    private $museumCategories;

    /**
     * @var array
     */
    private $museumProminence;

    public function __construct(Client $webCrawlerClient, array $museumCategories, array $museumProminence)
    {
        $this->webCrawlerClient = $webCrawlerClient;
        $this->museumCategories = $museumCategories;
        $this->museumProminence = $museumProminence;
    }

    /**
     * Find category for the museum. If append is set to true, the category will be set to the museum object.
     * If no category can be found, UNKNOWN will be used as fallback.
     *
     * @param Museum $museum
     * @param bool $append
     * @return string
     */
    public function findCategory(Museum $museum, $append = true) : string {
        $category = $this->museumCategories[$museum->getFid()] ?? 'UNKNOWN';

        if($append) {
            $museum->setCategory($category);
        }

        return $category;
    }

    /**
     * Find prominence for the museum. If append is set to true, the prominence will be set to the museum object.
     * If no prominence can be found, 0.5 will be used as fallback.
     *
     * @param Museum $museum
     * @param bool $append
     * @return string
     */
    public function findProminence(Museum $museum, $append = true) : float {
        $prominence = $this->museumProminence[$museum->getFid()] ?? 0.5;

        if($append) {
            $museum->setProminence($prominence);
        }

        return $prominence;
    }

    /**
     * Find features for the museum. If append is set to true, the features will be added to the museum object.
     *
     * @param Museum $museum
     * @param bool $append
     * @return MuseumFeature[]
     */
    public function findFeatures(Museum $museum, $append = true) : array {
        $features = [];
        // TODO: Find prominence for museum.

        if($append) {
            $museum->setFeatures($features);
        }

        return $features;
    }

}