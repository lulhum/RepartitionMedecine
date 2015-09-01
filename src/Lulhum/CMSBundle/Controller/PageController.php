<?php

namespace Lulhum\CMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Lulhum\CMSBundle\Entity\Page;

class PageController extends Controller
{
    
    public function pageAction(Page $page, $id)
    {
        if(!$page->isVisible($this->container->get('security.context'))) {
            throw new AccessDeniedHttpException;
        }
        
        return $this->render('LulhumCMSBundle:Page:page.html.twig', array(
            'page' => $page,
        ));
    }

    public function menuAction()
    {
        $menu = $this->getDoctrine()->getManager()->getRepository('LulhumCMSBundle:Page')->findMenu($this->container->get('security.context'));
            
        return $this->render('LulhumCMSBundle:Page:menu.html.twig', array(
            'menu' => $menu,
        ));
    }
}