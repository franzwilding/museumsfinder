<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Museum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    private function getResultsForm() : FormBuilder {
        return $this->createFormBuilder(NULL, ['csrf_protection'   => false])
            ->add('categories', ChoiceType::class, ['multiple' => true])
            ->add('tags', ChoiceType::class, ['multiple' => true])
            ->add('districts', ChoiceType::class, ['multiple' => true])
            ->add('uniqueness', NumberType::class)
            ->add('searchText', TextareaType::class);
    }

    private function getFeedbackForm() : FormBuilder {
        return $this->getResultsForm()->add('rating', NumberType::class);
    }

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        return [];
    }

    /**
     * @Route("/results", name="results")
     * @Method("POST")
     */
    public function resultsAction(Request $request) {

        $museums = [];
        $data = json_decode($request->getContent(), true);
        $form = $this->getResultsForm()->getForm();
        $form->submit($data);

        if($form->isValid() && $form->isSubmitted()) {
            // TODO: Get museums.

            $m = new Museum();
            $m
                ->setId(1)
                ->setAddress($data['tags'] ? $data['tags'][0] : 'Unknown')
                ->setCategory($data['categories'] ? $data['categories'][0] : 'Unknown')
                ->setDistrict($data['districts'] ? $data['districts'][0] : 'Unknown')
                ->setWeb('http://google.com')
                ->setName($data['searchText'] ? $data['searchText'] : 'Museum');
            $museums[] = $m;
        } else {
            return new JsonResponse((string)$form->getErrors(), 400);
        }

        return new Response($this->get('serializer')->serialize($museums, 'json', ['groups' => ['public']]));
    }

    /**
     * @Route("/feedback", name="feedback")
     * @Method("POST")
     */
    public function feedbackAction(Request $request) {

        $data = json_decode($request->getContent(), true);
        $form = $this->getFeedbackForm()->getForm();
        $form->submit($data);

        if($form->isValid() && $form->isSubmitted()) {
            // TODO: Save Feedback.

            return new Response('', 200);
        } else {
            return new JsonResponse((string)$form->getErrors(), 400);
        }
    }
}
