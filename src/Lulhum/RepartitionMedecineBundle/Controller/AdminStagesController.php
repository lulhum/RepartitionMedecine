<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminStagesController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryType;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategory;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryFilterType;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter;

class AdminStagesController extends Controller
{

    public function stageCategoriesAction(Request $request)
    {        
        $em = $this->getDoctrine()->getManager();
        $stageCategoryRepository = $em->getRepository('LulhumRepartitionMedecineBundle:StageCategory');
        
        $stageCategoryFilter = new StageCategoryFilter();
        $form = $this->createForm(new StageCategoryFilterType(), $stageCategoryFilter);

        $form->handleRequest($request);

        $stageCategories = $stageCategoryRepository->filteredFind($stageCategoryFilter);

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stagecategories.html.twig', array(
            'form' => $form->createView(),
            'stageCategories' => $stageCategories,
        ));
    }

    public function newStageCategoryAction(Request $request)
    {
        $stageCategory = new StageCategory();
        $form = $this->createForm(new StageCategoryType(), $stageCategory);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageCategory);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stagecategory.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editStageCategoryAction(Request $request, StageCategory $stageCategory, $id)
    {
        $form = $this->createForm(new StageCategoryType(), $stageCategory);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageCategory);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stagecategory.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
