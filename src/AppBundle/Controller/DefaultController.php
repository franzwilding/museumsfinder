<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Feedback;
use AppBundle\Entity\Museum;
use Elastica\Query;
use Elastica\Query\Match;
use Elastica\Rescore\Query as QueryRescore;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private function getResultsForm() : FormBuilderInterface {
        return $this->createFormBuilder(NULL, ['csrf_protection'   => false])
            ->add('categories', ChoiceType::class, ['multiple' => true, 'choices' => ['Wien']])
            ->add('tags', ChoiceType::class, ['multiple' => true, 'choices' => ['Barrierefrei']])
            ->add('districts', ChoiceType::class, ['multiple' => true, 'choices' => [10]])
            ->add('uniqueness', NumberType::class)
            ->add('searchText', TextareaType::class);
    }

    private function getFeedbackForm() : FormBuilderInterface {
        return $this->getResultsForm()
            ->add('rating', NumberType::class)
            ->add('museum', EntityType::class, ['class' => Museum::class, 'required' => true]);
    }

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction() {
        return [];
    }

    /**
     * @Route("/results", name="results")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function resultsAction(Request $request) {
        $data = json_decode($request->getContent(), true);
        $form = $this->getResultsForm()->getForm();
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


            $finder = $this->get('fos_elastica.finder.app.museum');
            $query = new Query();

            $mainQuery = new Query\BoolQuery();
            foreach($data['districts'] as $district) {
                $districtQuery = new Match();
                $districtQuery->setFieldQuery('district', $district);
                $mainQuery->addShould($districtQuery);
            }

            $ltrQuery = new Query([
                'query' => [
                    'ltr' => [
                        'model' => [ 'stored' => 'museum_ltr_model' ],
                        'features' => [
                            'match' => [
                                'name' => ' ' . $data['searchText'],
                            ]
                        ],
                    ]
                ]
            ]);
            $queryRescore = new QueryRescore($ltrQuery);

            $query->setQuery($mainQuery);
            $query->setRescore($queryRescore);

            /**
             * @var Museum[] $museums
             */
            $museums = $finder->find($query);

        } else {
            return new JsonResponse((string)$form->getErrors(true, true), 400);
        }

        return new Response($this->get('serializer')->serialize($museums, 'json', ['groups' => ['public']]));
    }

    /**
     * @Route("/feedback", name="feedback")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function feedbackAction(Request $request) {

        $data = json_decode($request->getContent(), true);
        $form = $this->getFeedbackForm()->getForm();
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $parameters = [
                '1' => 0,   // Categories
                '2' => 0,   // Tags
                '3' => 0,   // Uniqueness
                '4' => 0,   // SearchText
            ];
            $feedback = new Feedback();
            $feedback
                ->setMuseum($data['museum'])
                ->setRating($data['rating'])
                ->setParameters($parameters);

            // TODO: Get real parameter values by using the corresponding elasticsearch queries.

            $this->getDoctrine()->getManager()->persist($feedback);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 200);
        } else {
            return new JsonResponse((string)$form->getErrors(), 400);
        }
    }
}
