<?php
// src/Lulhum/RepartitionMedecineBundle/Controller/AdminStagesController.php

namespace Lulhum\RepartitionMedecineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Lulhum\DeadlineBundle\Entity\Deadline;
use Lulhum\DeadlineBundle\Form\DeadlineType;
use Lulhum\RepartitionMedecineBundle\Entity\Category;
use Lulhum\RepartitionMedecineBundle\Entity\Location;
use Lulhum\RepartitionMedecineBundle\Entity\Requirement;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategory;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter;
use Lulhum\RepartitionMedecineBundle\Entity\StageFilter;
use Lulhum\RepartitionMedecineBundle\Entity\Period;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposal;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalFilter;
use Lulhum\RepartitionMedecineBundle\Form\GroupActionType;
use Lulhum\RepartitionMedecineBundle\Form\RequirementType;
use Lulhum\RepartitionMedecineBundle\Form\RequirementParamsType;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryFilterType;
use Lulhum\RepartitionMedecineBundle\Form\StageCategoryType;
use Lulhum\RepartitionMedecineBundle\Form\StageFilterType;
use Lulhum\RepartitionMedecineBundle\Form\PeriodType;
use Lulhum\RepartitionMedecineBundle\Form\StageProposalType;
use Lulhum\RepartitionMedecineBundle\Form\StageProposalFilterType;
use Lulhum\RepartitionMedecineBundle\Form\StageType;
use Lulhum\RepartitionMedecineBundle\Util\Paginator;
use Lulhum\RepartitionMedecineBundle\Util\StageProposalGroupAction;
use Lulhum\RepartitionMedecineBundle\Util\StageGroupAction;
use Lulhum\UserBundle\Entity\User;

class AdminStagesController extends Controller
{

    public function stageCategoriesAction(Request $request, $page = 1)
    {        
        $em = $this->getDoctrine()->getManager();

        $session = new Session();
        if($session->has('adminStageCategoriesFilter') && $session->get('adminStageCategoriesFilter') instanceof StageCategoryFilter) {
            $stageCategoryFilter = $session->get('adminStageCategoriesFilter');
            // Merge all the filter entities into the entity manager
            foreach($stageCategoryFilter->getCollections() as $key => $collection) {
                call_user_func(array($stageCategoryFilter, 'set'.ucfirst($key)), call_user_func(array($stageCategoryFilter, 'get'.ucfirst($key)))->map(function($item) use (&$em) {
                    return $em->merge($item);
                }));
            }
        }
        else {
            $stageCategoryFilter = new StageCategoryFilter();
        }        

        $form = $this->createForm(new StageCategoryFilterType(), $stageCategoryFilter);

        $form->handleRequest($request);

        if($form->isValid()) {
            $session->set('adminStageCategoriesFilter', $stageCategoryFilter);
        }            

        $stageCategoryRepository = $em->getRepository('LulhumRepartitionMedecineBundle:StageCategory');
        
        $pagination = new Paginator(
            $em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('pagination')->getValue(),
            $stageCategoryRepository->filteredFindCount($stageCategoryFilter),
            $page,
            'lulhum_repartitionmedecine_admin_stage_categories_page'
        );

        $stageCategories = $stageCategoryRepository->filteredFind($stageCategoryFilter, $pagination->getMax(), $pagination->getOffset());

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stagecategories.html.twig', array(
            'form' => $form->createView(),
            'stageCategories' => $stageCategories,
            'pagination' => $pagination,
        ));
    }

    public function stageCategoriesInCategoryAction(Category $category, $id)
    {        
        $filter = new StageCategoryFilter();
        $filter->addCategoryOr($category);
        $session = new Session();
        $session->set('adminStageCategoriesFilter', $filter);

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
    }

    public function stageCategoriesInLocationAction(Location $location, $id)
    {
        $filter = new StageCategoryFilter();
        $filter->addLocation($location);
        $session = new Session();
        $session->set('adminStageCategoriesFilter', $filter);
        
        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
    }


    public function resetStageCategoryFilterAction()
    {
        $session = new Session();
        $session->remove('adminStageCategoriesFilter');

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_categories'));
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

    public function stageProposalsInPeriodAction(Period $period, $id)
    {
        $stageProposalFilter = new StageProposalFilter();
        $stageProposalFilter->addPeriod($period);
        $session = new Session();        
        $session->set('adminStageProposalsFilter', $stageProposalFilter);

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
    }

    public function stageProposalsAction(Request $request, $page = 1)
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

        $proposalRepository = $em->getRepository('LulhumRepartitionMedecineBundle:StageProposal');
        
        $pagination = new Paginator(
            $em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('pagination')->getValue(),
            $proposalRepository->filteredFindCount($stageProposalFilter),
            $page,
            'lulhum_repartitionmedecine_admin_stage_proposals_page'
        );

        $groupAction = new StageProposalGroupAction();
        $groupActionFormType = new GroupActionType(array(
            'filter' => $stageProposalFilter,
            'max' => $pagination->getMax(),
            'offset' => $pagination->getOffset())
        );
        $groupActionForm = $this->createForm($groupActionFormType, $groupAction);        

        if($request->getMethod() === 'POST' && $request->request->has($groupActionFormType->getName())) {                
            $groupActionForm->handleRequest($request);    
            if($groupActionForm->isValid()) {
                if($groupAction->getAction() === 'addRequirement') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_add_requirement'));
                }
                if($groupAction->getAction() === 'start') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_start'));
                }
                if($groupAction->getAction() === 'clone') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_clone'));
                }
                if($groupAction->getAction() === 'removeRequirement') {
                    $session->set('adminStageProposalsGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals_group_remove_requirement'));
                }
                $groupAction->executeAction();
                foreach($groupAction->getEntities() as $proposal) {
                    $em->persist($proposal);
                }
                $em->flush();
            }
        }

        $stageProposals = $proposalRepository->filteredFind($stageProposalFilter, $pagination->getMax(), $pagination->getOffset());

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
            'pagination' => $pagination,
        ));
    }

    public function stageProposalAction(StageProposal $proposal, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = array();
        foreach($em->getRepository('LulhumRepartitionMedecineBundle:Category')->findAll() as $category) {
            $categories[$category->getId()] = $category;
        }
        
        return $this->render('LulhumRepartitionMedecineBundle:Admin:viewstageproposal.html.twig', array(
            'proposal' => $proposal,
            'admin' => true,
            'categories' => $categories,
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

    public function stageProposalCloneAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $session = new Session();
        $groupAction = StageProposalGroupAction::merge($em, $session, 'adminStageProposalsGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
        }        

        $form = $this->get('form.factory')
                     ->createBuilder('form')
                     ->add('periods', 'entity', array(
                         'label' => 'Périodes',
                         'class' => 'LulhumRepartitionMedecineBundle:Period',
                         'multiple' => true,
                         'required' => false,
                     ))
                     ->add('new_periods', 'collection', array(
                         'label' => false,
                         'type' => new PeriodType(),
                         'allow_add' => true,
                         'allow_delete' => true,
                         'by_reference' => false,
                     ))
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            foreach(array_merge($form->get('periods')->getData()->toArray(), $form->get('new_periods')->getData()) as $period) {
                foreach($groupAction->getProposals() as $proposal) {
                    $newProposal = clone $proposal;
                    $newProposal->setPeriod($period);
                    $em->persist($newProposal);
                    $session->getFlashBag()->add('success', 'Proposition "'.$newProposal.'" créée.');
                }
            }
            $em->flush();
            
            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposalclone.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }


    public function stageProposalStartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $session = new Session();
        $groupAction = StageProposalGroupAction::merge($em, $session, 'adminStageProposalsGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
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

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposalstart.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }

    public function stageProposalAddRequirementAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $session = new Session();
        $groupAction = StageProposalGroupAction::merge($em, $session, 'adminStageProposalsGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
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

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposaladdrequirement.html.twig', array(
            'groupAction' => $groupAction,
            'form' => $form->createView(),
        ));
    }

    public function stageProposalRemoveRequirementAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $session = new Session();
        $groupAction = StageProposalGroupAction::merge($em, $session, 'adminStageProposalsGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
        } 
        
        $form = $this->get('form.factory')->createBuilder('form')
                     ->add('requirements', 'choice', array(
                         'label' => 'Types de contrainte',
                         'choices' => Requirement::TYPES,
                         'multiple' => true,                         
                     ))
                     ->add('valider', 'submit')
                     ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            foreach($groupAction->getProposals() as $proposal) {
                foreach($proposal->getRequirements() as $requirement) {
                    if(in_array($requirement->getType(), $form->get('requirements')->getData())) {
                        $em->remove($requirement);
                    }
                }
            }
            $em->flush();

            return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stageproposalremoverequirement.html.twig', array(
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

    public function noStagesFilterAction()
    {
        $session = new Session();
        $session->set('adminStagesFilter', new StageFilter());

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages'));
    }

    public function resetStageProposalsFilterAction()
    {
        $session = new Session();
        $session->remove('adminStageProposalsFilter');

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
    }

    public function noStageProposalsFilterAction()
    {
        $session = new Session();
        $session->set('adminStageProposalsFilter', new StageProposalFilter());

        return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_proposals'));
    }

    public function stagesAction(Request $request, $page = 1)
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

        $stageRepository = $em->getRepository('LulhumRepartitionMedecineBundle:Stage');
        
        $pagination = new Paginator(
            $em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('pagination')->getValue(),
            $stageRepository->filteredFindCount($stagesFilter),
            $page,
            'lulhum_repartitionmedecine_admin_stage_stages_page'
        );

        $groupAction = new StageGroupAction();
        $groupActionFormType = new GroupActionType(array(
            'filter' => $stagesFilter,
            'max' => $pagination->getMax(),
            'offset' => $pagination->getOffset(),
        ));
        $groupActionForm = $this->createForm($groupActionFormType, $groupAction);        

        if($request->getMethod() === 'POST' && $request->request->has($groupActionFormType->getName())) {                
            $groupActionForm->handleRequest($request);    
            if($groupActionForm->isValid()) {
                if($groupAction->getAction() === 'delete') {
                    $session->set('adminStagesGroupAction', $groupAction);

                    return $this->redirect($this->generateUrl('lulhum_repartitionmedecine_admin_stage_stages_group_delete'));
                }
                $groupAction->executeAction();
                foreach($groupAction->getEntities() as $stage) {
                    $em->persist($stage);
                }
                $em->flush();
            }
        }
        
        $stages = $stageRepository->filteredFind($stagesFilter, $pagination->getMax(), $pagination->getOffset());

        $requirements = array();
        foreach($stages as $stage) {
            $requirements[] = $this->get('lulhum_repartitionmedecine_stagevalidator')->checkRequirements($stage);
        }

        return $this->render('LulhumRepartitionMedecineBundle:Admin:stages.html.twig', array(
            'stages' => $stages,
            'filterForm' => $filterForm->createView(),
            'groupActionForm' => $groupActionForm->createView(),
            'requirements' => $requirements,
            'pagination' => $pagination,
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
        $groupAction = StageGroupAction::merge($em, $session, 'adminStagesGroupAction');
        if(is_null($groupAction)) {

            return $this->redirect($this->getRequest()->headers->get('referer'));
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

