<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminStagesController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryType;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategory;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryFilterType;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter;
use Lulhum\RepartitionMedecineBundle\Form\StageProposalType;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposal;
use Lulhum\RepartitionMedecineBundle\Form\StageProposalFilterType;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalFilter;
use Lulhum\RepartitionMedecineBundle\Form\StageProposalGroupActionType;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalGroupAction;
use Lulhum\RepartitionMedecineBundle\Form\RequirementType;
use Lulhum\RepartitionMedecineBundle\Form\RequirementParamsType;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;
use Lulhum\RepartitionMedecineBundle\Form\StageType;
use Lulhum\RepartitionMedecineBundle\Entity\StageFilter;
use Lulhum\RepartitionMedecineBundle\Form\StageFilterType;
use Lulhum\RepartitionMedecineBundle\Form\StageGroupActionType;
use Lulhum\RepartitionMedecineBundle\Entity\StageGroupAction;
use Lulhum\UserBundle\Entity\User;
use Lulhum\DeadlineBundle\Entity\Deadline;
use Lulhum\DeadlineBundle\Form\DeadlineType;

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

    public function stageCategoryAction(StageCategory $stageCategory, $id)
    {
        return $this->render('LulhumRepartitionMedecineBundle:Admin:viewstagecategory.html.twig', array(
            'stageCategory' => $stageCategory,
        ));
    }

    public function stageProposalsInCategoryAction(StageCategory $stageCategory, $id)
    {
        $stageProposalFilter = new StageProposalFilter();
        $stageProposalFilter->addStageCategory($stageCategory);
        $session = new Session();        
        $session->set('adminStageProposalsFilter', $stageProposalFilter);

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
    }

    public function stageProposalsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        if($session->has('adminStageProposalsFilter') && $session->get('adminStageProposalsFilter') instanceof StageProposalFilter) {
            $stageProposalFilter = $session->get('adminStageProposalsFilter');
            // Merge all the filter entities into the entity manager
            foreach($stageProposalFilter->getCollections() as $key => $collection) {
                call_user_func(array($stageProposalFilter, 'set'.ucfirst($key)), call_user_func(array($stageProposalFilter, 'get'.ucfirst($key)))->map(function($item) use (&$em) {
                    return $em->merge($item);
                }));
            }
        }
        else {
            $stageProposalFilter = new StageProposalFilter();
            foreach($em->getRepository('LulhumRepartitionMedecineBundle:Period')->findCurrents() as $period) {
                $stageProposalFilter->addPeriod($period);
            }
        }
                                 
        $filterFormType = new StageProposalFilterType();
        $filterForm = $this->createForm($filterFormType, $stageProposalFilter);
        if($request->getMethod() === 'POST' && $request->request->has($filterFormType->getName())) {
                $filterForm->handleRequest($request);
                $session->set('adminStageProposalsFilter', $stageProposalFilter);
        }

        $groupAction = new StageProposalGroupAction();
        $groupActionFormType = new StageProposalGroupActionType(array('filter' => $stageProposalFilter));
        $groupActionForm = $this->createForm($groupActionFormType, $groupAction);        

        if($request->getMethod() === 'POST' && $request->request->has($groupActionFormType->getName())) {                
            $groupActionForm->handleRequest($request);    
            if($groupActionForm->isValid()) {
                if($groupAction->getAction() === 'addConstraint') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_add_constraint'));
                }
                if($groupAction->getAction() === 'start') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_start'));
                }
                $groupAction->executeAction();
                foreach($groupAction->getProposals() as $proposal) {
                    $em->persist($proposal);
                }
                $em->flush();
            }
        }

        $stageProposals = $em->getRepository('LulhumRepartitionMedecineBundle:StageProposal')->filteredFind($stageProposalFilter);

        $categories = array();
        foreach($em->getRepository('LulhumRepartitionMedecineBundle:Category')->findAll() as $category) {
            $categories[$category->getId()] = $category;
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposals.html.twig', array(
            'stageProposals' => $stageProposals,
            'filterForm' => $filterForm->createView(),
            'groupActionForm' => $groupActionForm->createView(),
            'categories' => $categories,
            'promotions' => User::PROMOTIONS,
        ));
    }

    public function newStageProposalAction(Request $request)
    {
        $stageProposal = new StageProposal();
        $form = $this->createForm(new StageProposalType(), $stageProposal);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageProposal);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposal.html.twig', array(
            'form' => $form->createView(),
            'proposal' => $stageProposal,
        ));
    }

    public function editStageProposalAction(Request $request, StageProposal $stageProposal, $id)
    {
        $form = $this->createForm(new StageProposalType(), $stageProposal);

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stageProposal);
            
            foreach($em->getRepository('LulhumRepartitionMedecineBundle:Requirement')->findByProposal($stageProposal) as $requirement) {
                if(!$stageProposal->getRequirements()->contains($requirement)) {
                    $em->remove($requirement);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposal.html.twig', array(
            'form' => $form->createView(),
            'proposal' => $stageProposal,
        ));
    }

    public function stageProposalStartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        if($session->has('adminStageProposalsGroupAction') && $session->get('adminStageProposalsGroupAction') instanceof StageProposalGroupAction) {
            $groupAction = $session->get('adminStageProposalsGroupAction');
            if($groupAction->getProposals()->count() == 0) {

                return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
            }
            $groupAction->setProposals($groupAction->getProposals()->map(function($item) use (&$em) {
                return $em->merge($item);
            }));
        }
        else {

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        $deadline = new Deadline();
        $form = $this->createForm(new DeadlineType(array(array(
            'field' => 'autovalid',
            'type' => 'checkbox',
            'options' => array(
                'mapped' => false,
                'label' => 'Accepter automatiquement les stages valides',
                'required' => false,
            )))), $deadline);

        $form->handleRequest($request);

        if($form->isValid()) {

            $args = array('this');
            if($form->get('autovalid')->getData()) {
                $args[] = 'autovalid';
            }
            $deadline->setActive(true);
            $deadline->setCallback('lulhum_repartitionmedecine_repartition');
            $deadline->setCallbackParams(array(
                'method' => 'closeRepartition',
                'args' => $args,
            ));
            $em->persist($deadline);

            foreach($groupAction->getProposals() as $proposal) {
                $proposal->setDeadline($deadline);
                $proposal->setLocked(false);
                $em->persist($proposal);
            }

            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:startstageproposal.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }

    public function stageProposalAddConstraintAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        if($session->has('adminStageProposalsGroupAction') && $session->get('adminStageProposalsGroupAction') instanceof StageProposalGroupAction) {
            $groupAction = $session->get('adminStageProposalsGroupAction');
            if($groupAction->getProposals()->count() == 0) {

                return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
            }
            $groupAction->setProposals($groupAction->getProposals()->map(function($item) use (&$em) {
                return $em->merge($item);
            }));
        }
        else {

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }
        
        $form = $this->get('form.factory')->createBuilder('form')
                     ->add('requirements', 'collection', array(
                         'label' => false,
                         'type' => new RequirementType(),
                         'allow_add' => true,
                     ))
                     ->add('valider', 'submit')
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {            
            foreach($groupAction->getProposals() as $proposal) {
                foreach($form['requirements'] as $requirement) {
                    $proposal->addRequirement(clone $requirement->getData());
                }
                $em->persist($proposal);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposaladdconstraint.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }

    public function getRequirementFormAction(Request $request)
    {
        if($request->isXmlHttpRequest()) {
            $type = '';
            $type = $request->request->get('paramType');
            $proposalId = '';
            $proposalId = $request->request->get('proposal');

            $form = $this->createForm(new RequirementParamsType(array(
                'paramType' => $type,
                'proposal' => $proposalId,
            )));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:requirementform.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function stagesInProposalAction(StageProposal $stageProposal, $id)
    {        
        $filter = new StageFilter();
        $filter->addStageProposal($stageProposal);
        $session = new Session();
        $session->set('adminStagesFilter', $filter);

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
    }

    public function resetStagesFilterAction()
    {
        $session = new Session();
        $session->remove('adminStagesFilter');

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
    }

    public function resetStageProposalsFilterAction()
    {
        $session = new Session();
        $session->remove('adminStageProposalsFilter');

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
    }

    public function resetStageCategoriesFilterAction()
    {
        $session = new Session();
        $session->remove('adminStageCategoriesFilter');

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
    }
        

    public function stagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session = new Session();
        if($session->has('adminStagesFilter') && $session->get('adminStagesFilter') instanceof StageFilter) {
            $stagesFilter = $session->get('adminStagesFilter');
            // Merge all the filter entities into the entity manager
            foreach($stagesFilter->getCollections() as $key => $collection) {
                call_user_func(array($stagesFilter, 'set'.ucfirst($key)), call_user_func(array($stagesFilter, 'get'.ucfirst($key)))->map(function($item) use (&$em) {
                    $managedItem = $em->merge($item);
                    $em->refresh($managedItem);
                    return $managedItem;
                }));
            }
        }
        else {
            $stagesFilter = new StageFilter();
            foreach($em->getRepository('LulhumRepartitionMedecineBundle:Period')->findCurrents() as $period) {
                $stagesFilter->addPeriod($period);
            }
        }
                                 
        $filterFormType = new StageFilterType();
        $filterForm = $this->createForm($filterFormType, $stagesFilter);
        if($request->getMethod() === 'POST' && $request->request->has($filterFormType->getName())) {
                $filterForm->handleRequest($request);
                $session->set('adminStagesFilter', $stagesFilter);
        }

        $groupAction = new StageGroupAction();
        $groupActionFormType = new StageGroupActionType(array('filter' => $stagesFilter));
        $groupActionForm = $this->createForm($groupActionFormType, $groupAction);        

        if($request->getMethod() === 'POST' && $request->request->has($groupActionFormType->getName())) {                
            $groupActionForm->handleRequest($request);    
            if($groupActionForm->isValid()) {
                if($groupAction->getAction() === 'delete') {
                    $session->set('adminStagesGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages_group_delete'));
                }
                $groupAction->executeAction();
                foreach($groupAction->getStages() as $stage) {
                    $em->persist($stage);
                }
                $em->flush();
            }
        }
        
        $stages = $em->getRepository('LulhumRepartitionMedecineBundle:Stage')->filteredFind($stagesFilter);

        $requirements = array();
        foreach($stages as $stage) {
            $requirements[] = $this->get('lulhum_repartitionmedecine_stagevalidator')->checkRequirements($stage);
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stages.html.twig', array(
            'stages' => $stages,
            'filterForm' => $filterForm->createView(),
            'groupActionForm' => $groupActionForm->createView(),
            'requirements' => $requirements,
        ));
    }

    public function newStageAction(Request $request)
    {
        $stage = new Stage();
        $form = $this->createForm(new StageType(), $stage);
        
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($stage);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stage.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteStageAction(Request $request, Stage $stage, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($stage);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirmdelete.html.twig', array(
            'form' => $form->createView(),
            'stages' => array($stage),
            'proposals' => array(),
            'categories' => array(),
        ));
    }

    public function deleteStagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session = new Session();
        if($session->has('adminStagesGroupAction') && $session->get('adminStagesGroupAction') instanceof StageGroupAction) {
            $groupAction = $session->get('adminStagesGroupAction');
            if($groupAction->getStages()->count() == 0) {

                return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
            }
            $groupAction->setStages($groupAction->getStages()->map(function($item) use (&$em) {
                return $em->merge($item);
            }));
        }
        else {

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
        }

        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach($groupAction->getStages() as $stage) {
                $em->remove($stage);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirmdelete.html.twig', array(
            'form' => $form->createView(),
            'stages' => $groupAction->getStages(),
            'proposals' => array(),
            'categories' => array(),
        ));
    }

    public function deleteStageProposalAction(Request $request, StageProposal $proposal, $id)
    {
        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('Confirmer', 'submit', array('attr' => array('class' => 'btn btn-danger')))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($proposal);
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirmdelete.html.twig', array(
            'form' => $form->createView(),
            'stages' => $proposal->getStages(),
            'proposals' => array($proposal),
            'categories' => array(),
        ));
    }

    public function deleteStageCategoryAction(Request $request, StageCategory $category, $id)
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

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
        }

        $stages = array();
        foreach($category->getProposals() as $proposal) {
            $stages = array_merge($stages, $proposal->getStages()->toArray());
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:confirmdelete.html.twig', array(
            'form' => $form->createView(),
            'stages' => $stages,
            'proposals' => $category->getProposals(),
            'categories' => array($category),
        ));
    }
        
}

