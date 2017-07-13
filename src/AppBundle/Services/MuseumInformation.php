<?php

namespace AppBundle\Services;

use AppBundle\Entity\Feature;
use AppBundle\Entity\Museum;
use AppBundle\Entity\MuseumFeature;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

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
    private $museumUniqueness;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(Client $webCrawlerClient, array $museumCategories, array $museumUniqueness, EntityManager $em)
    {
        $this->webCrawlerClient = $webCrawlerClient;
        $this->museumCategories = $museumCategories;
        $this->museumUniqueness = $museumUniqueness;
        $this->em = $em;
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
     * Find uniqueness for the museum. If append is set to true, the uniqueness will be set to the museum object.
     * If no uniqueness can be found, 0.5 will be used as fallback.
     *
     * @param Museum $museum
     * @param bool $append
     * @return string
     */
    public function findUniqueness(Museum $museum, $append = true) : float {
        $uniqueness = $this->museumUniqueness[$museum->getFid()] ?? 0.5;

        if($append) {
            $museum->setUniqueness($uniqueness);
        }

        return $uniqueness;
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

        if($web = $museum->getWeb()) {
            try {
                $body = $this->webCrawlerClient->get($museum->getWeb())->getBody()->getContents();
                $crawler = new Crawler($body);
                $crawler->filter('script')->each(function ($crawler) {
                    foreach ($crawler as $node) {
                        $node->parentNode->removeChild($node);
                    }
                });

                $text = $crawler->text();
                $features = $this->extractFeaturesFromText(strtolower(str_replace('  ', ' ', $text)));

                if($append) {
                    foreach($features as $feature => $rating) {
                        $this->addFeature($museum, $feature, $rating);
                    }
                }

            } catch (\Exception $e) {
                echo "\n[ERROR]: Could not parse website. Error: " . $e->getMessage();
            }
        }

        $museum->setWebCrawled(new \DateTime('now'));
        return $features;
    }

    private function extractFeaturesFromText($text) {
        $features = [];


        if(strpos($text, 'freier eintritt') >= 0) {
            $features[Feature::FEATURE_FREIER_EINTRITT] = 1;
        }

        if(strpos($text, 'barrierefrei') >= 0) {
            $features[Feature::FEATURE_BARRIEREFREI] = 0.8;
        }

        if(strpos($text, 'english') >= 0) {
            $features[Feature::FEATURE_ENGLISCH] = 0.5;
        }

        return $features;
    }

    private function addFeature(Museum $museum, $feature_name, $rating) {
        foreach($museum->getFeatures() as $museumFeature) {
            if($museumFeature->getFeature()->getName() === $feature_name) {
                $museumFeature->setRating($rating);
                $this->em->persist($museumFeature);
                return;
            }
        }

        // Check if feature exists.
        if(!($feature = $this->em->getRepository('AppBundle:Feature')->findOneBy(['name' => $feature_name]))) {
            $feature = new Feature();
            $feature->setName($feature_name);
            $this->em->persist($feature);
            $this->em->flush($feature);
        }

        // Create museum feature.
        $museumFeature = new MuseumFeature();
        $museumFeature->setFeature($feature)->setMuseum($museum);
        $museumFeature->setRating($rating);
        $museum->getFeatures()->add($museumFeature);
    }

    public function featureQueries($searchData) {

        // Categories
        $f1 = ['bool' => ['should' => []]];
        foreach($searchData['categories'] as $category) {
            $f1['bool']['should'][] = ['match' => ['category' => $category]];
        }

        // Tags
        $f2 = ['bool' => ['should' => []]];
        foreach($searchData['tags'] as $tag) {
            $f2['bool']['should'][] = ['match' => ['tags' => $tag]];
        }

        // Uniqueness
        $f3 = ['match' => ['uniqueness' => $searchData['uniqueness']]];

        // searchText
        $f4 = ['match_phrase' => [
            'name' => ' ' . $searchData['searchText'],
        ]];

        return [$f1, $f2, $f3, $f4];
    }

}