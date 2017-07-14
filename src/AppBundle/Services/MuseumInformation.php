<?php

namespace AppBundle\Services;

use AppBundle\Entity\Feature;
use AppBundle\Entity\Museum;
use AppBundle\Entity\MuseumFeature;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    public function __construct(Client $webCrawlerClient, $rootDir, EntityManager $em)
    {
        $this->webCrawlerClient = $webCrawlerClient;
        $this->em = $em;

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder(';')]);
        $infos = $serializer->decode(file_get_contents($rootDir . '/config/museum_information.csv'), 'csv');
        foreach($infos as $info) {
            $this->museumCategories[$info['FID']] = $info['CATEGORY'];
            $this->museumUniqueness[$info['FID']] = (float)$info['UNIQUENESS'];
        }
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

    public function regularQueries($searchData) {

        // Categories
        $f1 = ['bool' => ['should' => [], 'boost' => 1]];
        foreach($searchData['categories'] as $category) {
            $f1['bool']['should'][] = ['match' => ['category' => $category]];
        }

        // Tags
        $f2 = ['bool' => ['should' => [], 'boost' => 1]];
        foreach($searchData['tags'] as $tag) {
            $f2['bool']['should'][] = ['match' => ['tags' => $tag]];
        }

        // Uniqueness
        $f3 = [
            'function_score' => [
                'boost' => 2,
                'gauss' => [
                    'uniqueness' => [
                        'origin' => ($searchData['uniqueness'] / 100),
                        'scale' => 0.5,
                        'decay' => 0.5,
                    ]
                ]
            ]
        ];

        return [$f1, $f2, $f3];
    }

    public function featureQueries($searchData) {

        // searchText
        $f4 = ['match_phrase' => [
            'name' => ' ' . $searchData['searchText'],
        ]];

        return array_merge($this->regularQueries($searchData), [$f4]);
    }

}