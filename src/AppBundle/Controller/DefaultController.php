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

        $districts = [];
        for($i = 1; $i <= 23; $i++) {
            $districts[] = $i;
        }

        return $this->createFormBuilder(NULL, ['csrf_protection'   => false])
            ->add('categories', ChoiceType::class, ['multiple' => true, 'choices' => $this->getParameter('museum_categories')])
            ->add('tags', ChoiceType::class, ['multiple' => true, 'choices' => $this->getParameter('museum_tags')])
            ->add('districts', ChoiceType::class, ['multiple' => true, 'choices' => $districts])
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
        return [
            'tags' => $this->getParameter('museum_tags'),
            'categories' => $this->getParameter('museum_categories'),
        ];
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
                        'features' => $this->get('museum_information')->featureQueries($data),
                    ]
                ]
            ]);
            $queryRescore = new QueryRescore($ltrQuery);

            $query->setQuery($mainQuery);
            $query->setRescore($queryRescore);


            $museums = [];
            $response = $this->get('fos_elastica.index.app.museum')->search($query);
            foreach($response->getResults() as $result) {
                $data = $result->getData();
                $museum = new Museum($data['name'], $data['address'], $data['web']);
                $museum->setId($result->getId());
                $museum->relevance = $result->getScore() / $response->getMaxScore();
                $museums[] = $museum;
            }

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
            $feedback = new Feedback();
            $feedback
                ->setMuseum($data['museum'])
                ->setRating($data['rating'])
                ->setParameters($data);

            $this->getDoctrine()->getManager()->persist($feedback);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 200);
        } else {
            return new JsonResponse((string)$form->getErrors(), 400);
        }
    }
}
