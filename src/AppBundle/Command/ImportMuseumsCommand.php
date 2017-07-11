<?php

namespace AppBundle\Command;

use AppBundle\Entity\Museum;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportMuseumsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('museum:import')
            ->setDescription('Clear and (re-)import museums, crawl additional information from museum websites.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $museumRepo = $this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Museum');

        $total = 0;
        $imported = 0;
        $additional = 0;
        $list = [];

        /*** @var Museum[] $museums */
        $museums = [];
        $resource = $this->getContainer()->getParameter('dataset');

        /*** @var Client $openDataClient */
        $openDataClient = $this->getContainer()->get('guzzle.client.open_data');

        /*** @var Client $webCrawlerClient */
        $webCrawlerClient = $this->getContainer()->get('guzzle.client.web_crawler');

        $museumInformation = $this->getContainer()->get('museum_information');





        $output->writeln('');
        $output->writeln('Getting museums list from https://www.data.gv.at...');
        $response = \GuzzleHttp\json_decode($openDataClient->get($resource)->getBody()->getContents());
        $list = $response->features;
        $total = count($list);
        $output->writeln(["<info>[SUCCESS]</info> Found <info>$total</info> museums.", '']);




        $output->writeln('');
        $output->writeln(['Importing museums...', '']);
        $urlFixes = $this->getContainer()->getParameter('museum_url_fix');
        foreach($list as $m) {
            try {
                $web = array_key_exists($m->id, $urlFixes) ? $urlFixes[$m->id] : $m->properties->WEITERE_INF;
                $museum = $museumRepo->findOneBy(['fid' => $m->id]) ?? new Museum();
                $museum
                    ->setFid($m->id)
                    ->setWeb($web)
                    ->setName($m->properties->NAME)
                    ->setDistrict($m->properties->BEZIRK)
                    ->setAddress($m->properties->ADRESSE);

                $museumInformation->findCategory($museum);
                $museumInformation->findUniqueness($museum);

                if(!$museum->getId()) {
                    $this->getContainer()->get('doctrine.orm.default_entity_manager')->persist($museum);
                }

                $output->write('.');
                $imported++;
                $museums[] = $museum;

            } catch (\Exception $e) {
                $output->writeln('<error>Error: Could not save museum to database. Exception: ' . $e->getMessage() . '</error>');
            }
        }

        $this->getContainer()->get('doctrine.orm.default_entity_manager')->flush();
        $output->writeln(['', "<info>[SUCCESS]</info> Imported <info>$imported</info> out of <info>$total</info> museums.", '']);





        $output->writeln('');
        $output->writeln(['Fetching additional information for the next 10 museums from website...', '']);
        $museums = $museumRepo->findBy([], ['webCrawled' => 'ASC'], 10);
        foreach($museums as $museum) {
            if(count($museumInformation->findFeatures($museum)) > 0) {
                $additional++;
                $output->write('.');
            }
        }
        $output->writeln(['', "<info>[SUCCESS]</info> Fetched additional information for <info>$additional</info> museums.", '']);




        $output->writeln('');
        $this->getContainer()->get('doctrine.orm.default_entity_manager')->flush();
        $output->writeln("<info>[SUCCESS]</info> <info>Successfully imported $imported out of $total museums. Found additional information on the website for $additional museums.</info>");
    }
}
