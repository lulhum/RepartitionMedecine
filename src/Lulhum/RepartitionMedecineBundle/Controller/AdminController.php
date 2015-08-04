<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\RepartitionMedecineBundle\Entity\ParameterBag;
use Lulhum\RepartitionMedecineBundle\Form\ParameterBagType;

class AdminController extends Controller
{

    public function indexAction()
    {
        return $this->render('LulhumRepartitionMedecineBundle:Admin:index.html.twig');
    }

    public function repartitionParametersAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $parameterRepository = $em->getRepository('LulhumRepartitionMedecineBundle:Parameter');

        $parameters = new ParameterBag();
        $parameters->getParameters()->add($parameterRepository->findOneByName('groupRepartitionMode'));
        
        $form = $this->createForm(new ParameterBagType(), $parameters);

        $form->handleRequest($request);

        if($form->isValid()) {
            foreach($parameters as $parameter) {
                $em->persist($parameter);
            }
            $em->flush();
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:parameters.html.twig', array(
            'form' => $form->createView(),
            'parameterGroup' => 'Répartition',
        ));            
    }

}