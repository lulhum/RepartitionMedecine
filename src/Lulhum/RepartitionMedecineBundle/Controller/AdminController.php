<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\RepartitionMedecineBundle\Entity\Category;
use Lulhum\RepartitionMedecineBundle\Entity\Location;
use Lulhum\RepartitionMedecineBundle\Entity\ParameterBag;
use Lulhum\RepartitionMedecineBundle\Entity\Period;
use Lulhum\RepartitionMedecineBundle\Form\CategoryType;
use Lulhum\RepartitionMedecineBundle\Form\LocationType;
use Lulhum\RepartitionMedecineBundle\Form\ParameterBagType;
use Lulhum\RepartitionMedecineBundle\Form\PeriodType;

class AdminController extends Controller
{

    public function repartitionParametersAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $parameterRepository = $em->getRepository('LulhumRepartitionMedecineBundle:Parameter');

        $parameters = new ParameterBag();
        $parameters->getParameters()->add($parameterRepository->findOneByName('groupRepartitionMode'));
        $parameters->getParameters()->add($parameterRepository->findOneByName('pagination'));
        $parameters->getParameters()->add($parameterRepository->findOneByName('allowUserRegistrations'));
        $parameters->getParameters()->add($parameterRepository->findOneByName('siteTitle'));
        $parameters->getParameters()->add($parameterRepository->findOneByName('siteHomepage'));
        
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
        ));            
    }

    public function categoriesAction() {

        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('LulhumRepartitionMedecineBundle:Category')->findBy(array(), array('name' => 'ASC'));

        return $this->render('LulhumRepartitionMedecineBundle:Admin:categories.html.twig', array(
            'categories' => $categories,
        ));
    }

    public function editCategoryAction(Request $request, Category $category, $id)
    {
        $form = $this->createForm(new CategoryType(), $category);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_categories'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:editcategory.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteCategoryAction(Request $request, Category $category, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_categories'));
        }

        $message = 'Vous êtes sur le point de supprimer la catégorie ';
        $message .= $category;
        $message .= ' utilisée dans '.$category->getStageCategories()->count().' modèles.';

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

    public function periodsAction()
    {

        $em = $this->getDoctrine()->getManager();

        $periods = $em->getRepository('LulhumRepartitionMedecineBundle:Period')->findBy(array(), array('start' => 'ASC', 'stop' => 'ASC'));

        return $this->render('LulhumRepartitionMedecineBundle:Admin:periods.html.twig', array(
            'periods' => $periods,
        ));
    }

    public function editPeriodAction(Request $request, Period $period, $id)
    {
        $form = $this->createForm(new PeriodType(), $period);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($period);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_periods'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:editperiod.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deletePeriodAction(Request $request, Period $period, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($period);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_periods'));
        }

        $message = 'Vous êtes sur le point de supprimer la période '.$period;

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

    public function locationsAction()
    {

        $em = $this->getDoctrine()->getManager();

        $locations = $em->getRepository('LulhumRepartitionMedecineBundle:Location')->findBy(array(), array('name' => 'ASC'));

        return $this->render('LulhumRepartitionMedecineBundle:Admin:locations.html.twig', array(
            'locations' => $locations,
        ));
    }

    public function editLocationAction(Request $request, Location $location, $id)
    {
        $form = $this->createForm(new LocationType(), $location);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_locations'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:editlocation.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteLocationAction(Request $request, Location $location, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($location);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_locations'));
        }

        $message = 'Vous êtes sur le point de supprimer le lieu "'.$location.'"';

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirm.html.twig', array(
            'form' => $form->createView(),
            'message' => $message,
        ));
    }

}
