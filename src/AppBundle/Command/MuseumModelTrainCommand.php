<?php

namespace AppBundle\Command;

use AppBundle\Entity\Museum;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class MuseumModelTrainCommand extends ContainerAwareCommand
{
    private $root;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('museum:model:train')
            ->setDescription('Updates the ranking model and send it to the elasticsearch server');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->root = $this->getContainer()->getParameter('kernel.root_dir') . '/../';

        $output->writeln("Creating Trainings data file...");
        $this->_createTrainingDataFile();
        $output->writeln("<info>[SUCCESS]</info> Trainings data file was created successfully.");


        $output->writeln("Training rank model...");
        $this->_trainRankModel();
        $output->writeln("<info>[SUCCESS]</info> Rank model was trained successfully.");


        $output->writeln("Sending rank model to elasticsearch...");
        $this->_sendModelToElasticSearch();
        $output->writeln("<info>[SUCCESS]</info> Rank model was send to elasticsearch successfully.");
    }

    private function _createTrainingDataFile() {

        $training = '';
        foreach($this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Feedback')->findAll() as $feedback) {
            $rating = $feedback->getRating();
            $features = [];
            foreach($feedback->getParameters() as $feature => $value) {
                $features[] = $feature . ':' . $value;
            }
            $features_string = implode(' ', $features);
            $museum = $feedback->getMuseum()->getName();
            $training .= "$rating $features_string # $museum\n";
        }

        if(strlen($training) == 0) {
            $training = '0 qid:1 #Empty';
        }

        try {
            file_put_contents($this->root . 'var/museum/training.txt', $training);
        } catch (IOExceptionInterface $e) {
            echo "An error occurred while creating your directory at ".$e->getPath();
        }
    }

    private function _trainRankModel() {
        $process = new Process('java -jar ./bin/ranklib.jar -train ' . $this->root . 'var/museum/training.txt -ranker 6 -save ' . $this->root . 'var/museum/model.txt');
        $process->run();
    }

    private function _sendModelToElasticSearch() {
        $model_script = file_get_contents($this->root . 'var/museum/model.txt');
        $this->getContainer()->get('fos_elastica.client')->request('_scripts/ranklib/museum_ltr_model', \Elastica\Request::POST, [
            'script' => $model_script,
        ]);
    }
}
