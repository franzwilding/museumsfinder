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
     * @return string[]
     */
    public function findTags(Museum $museum, $append = true) : array {
        $tags = [];

        if($web = $museum->getWeb()) {
            try {
                if(empty($museum->getWebContent()) || $museum->getWebCrawled()->diff(new \DateTime('now'))->days > 1000) {
                    $crawler = new Crawler($this->webCrawlerClient->get($museum->getWeb())->getBody()->getContents());
                    $crawler->filter('script')->each(function ($crawler) {
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    });

                    $text = $crawler->text();
                    $text = strtolower(str_replace('  ', ' ', $text));
                    $museum->setWebContent(utf8_encode($text));
                }

                $tags = $this->extractTagsFromText($museum->getWebContent());

            } catch (\Exception $e) {
                echo "\n[ERROR]: Could not parse website. Error: " . $e->getMessage();
                $museum->setWebContent('[ERROR]');
            }
        }

        if($append) {
            $museum->setTags($tags);
        }

        $museum->setWebCrawled(new \DateTime('now'));
        return $tags;
    }

    private function extractTagsFromText($text) {
        $tags = [];


        if(strpos($text, 'freier eintritt') !== false) {
            $tags[] = 'Freier Eintritt';
        }

        if(strpos($text, 'barrierefrei') !== false) {
            $tags[] = 'Barrierefrei';
        }

        if(strpos($text, 'english') !== false) {
            $tags[] = 'Auf Englisch';
        }

        return $tags;
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