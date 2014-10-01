<?php

/*
* Copyright 2014 HLP-Nebula authors, see NOTICE file
4
*
* Licensed under the EUPL, Version 1.1 or – as soon they
will be approved by the European Commission - subsequent
versions of the EUPL (the "Licence");
* You may not use this work except in compliance with the
Licence.
* You may obtain a copy of the Licence at:
*
*
http://ec.europa.eu/idabc/eupl
5
*
* Unless required by applicable law or agreed to in
writing, software distributed under the Licence is
distributed on an "AS IS" basis,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
express or implied.
* See the Licence for the specific language governing
permissions and limitations under the Licence.
*/ 

namespace HLP\NebulaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Session;

use HLP\NebulaBundle\Entity\FSMod;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Form\FSModEditType;
use HLP\NebulaBundle\Form\BranchType;

class ModController extends Controller
{
  public function indexAction($owner, $mod)
  {
    return $this->redirect($this->generateUrl('hlp_nebula_mod_branches', array('owner' => $owner, 'mod' => $mod)), 301);
  }
  
  public function branchesAction(FSMod $mod, $page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("Page ".$page." does not exist.");
    }
    
    $owner = $mod->getOwner();
    
    $session = new Session();
    $session->set('branchEditRefer', 'fromList');
    
    $branchesPerPage = 10;
    $branchesAll = $mod->getBranches()->toArray();
    $nbPages = ceil(count($branchesAll)/$branchesPerPage);
    
    if (($page > $nbPages) && ($page > 1)) {
      throw $this->createNotFoundException("Page ".$page." does not exist.");
    }
    
    $branchesList = array_slice($branchesAll, ($page-1)*$branchesPerPage, $branchesPerPage);
    
    return $this->render('HLPNebulaBundle:AdvancedUI:mod_branches.html.twig', array(
      'owner' => $owner,
      'mod' => $mod,
      'branchesList' => $branchesList,
      'page' => $page,
      'nbPages' => $nbPages));
  }
  
  public function detailsAction(FSMod $mod)
  {
    $owner = $mod->getOwner();
    
    $session = new Session();
    $session->set('modEditRefer', 'fromDetails');
  
    return $this->render('HLPNebulaBundle:AdvancedUI:mod_details.html.twig', array('owner' => $owner, 'mod' => $mod));
  }
  
  public function activityAction(FSMod $mod)
  {
    $owner = $mod->getOwner();
  
    return $this->render('HLPNebulaBundle:AdvancedUI:mod_activity.html.twig', array('owner' => $owner, 'mod' => $mod));
  }
  
  public function newBranchAction(Request $request, FSMod $mod)
  {
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $branch = new Branch;
    $branch->setMod($mod);
    $form = $this->createForm(new BranchType(), $branch);

    $form->handleRequest($request);

    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($branch);
      $em->flush();
      
      $request->getSession()->getFlashBag()->add('success', "New branch <strong>".$branch->getName()."</strong> successfully created.");

      return $this->redirect($this->generateUrl('hlp_nebula_branch', array('owner' => $owner->getNameCanonical(), 'mod' => $mod->getModId(), 'branch' => $branch->getBranchId())));
    }
    
    return $this->render('HLPNebulaBundle:AdvancedUI:new_branch.html.twig', array(
      'owner' => $owner,
      'mod'    => $mod,
      'form'   => $form->createView()
    ));
  }
  
  public function editAction(Request $request, FSMod $mod)
  {
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $session = new Session();
    $refer = $session->get('modEditRefer');
    
    if($refer == 'fromDetails')
    {
      $referURL = $this->generateUrl('hlp_nebula_mod_details', array('mod' => $mod->getModId(), 'owner' => $owner->getNameCanonical()));
    }
    else
    {
      $referURL = $this->generateUrl('hlp_nebula_owner_mods', array('owner' => $owner->getNameCanonical()));
    }

    $form = $this->createForm(new FSModEditType(), $mod);

    if ($form->handleRequest($request)->isValid()) {
      //$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      $em = $this->getDoctrine()->getManager();

      $em->flush();
      
      $request->getSession()->getFlashBag()->add('success', "Mod <strong>".$mod->getTitle()."</strong> successfully edited.");

      return $this->redirect($referURL);
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:edit_mod.html.twig', array(
      'owner' => $owner,
      'mod' => $mod,
      'form' => $form->createView(),
      'referURL' => $referURL
    ));
  }
  
  public function deleteAction(Request $request, FSMod $mod)
  {
    $owner = $mod->getOwner();

    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }

    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($mod);
      $em->flush();

      $request->getSession()->getFlashBag()->add('success', "Mod <strong>".$mod->getTitle()."</strong> has been deleted.");
      
      return $this->redirect($this->generateUrl('hlp_nebula_owner', array('owner' => $owner->getNameCanonical())));
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:delete_mod.html.twig', array(
      'owner' => $owner,
      'mod' => $mod,
      'form' => $form->createView()
    ));
  }
  
}
