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

use HLP\NebulaBundle\Entity\FSMod;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Form\FSModType;
use HLP\NebulaBundle\Form\BranchType;

class ModController extends Controller
{
  public function indexAction($owner, $mod)
  {
    return $this->redirect($this->generateUrl('hlp_nebula_mod_branches', array('owner' => $owner, 'mod' => $mod)), 301);
  }
  
  public function branchesAction(FSMod $mod)
  {
    $owner = $mod->getOwner();
  
    return $this->render('HLPNebulaBundle:AdvancedUI:mod_branches.html.twig', array('owner' => $owner, 'mod' => $mod, 'branchesList' => $mod->getBranches()));
  }
  
  public function metadataAction(FSMod $mod)
  {
    $owner = $mod->getOwner();
  
    return $this->render('HLPNebulaBundle:AdvancedUI:mod_metadata.html.twig', array('owner' => $owner, 'mod' => $mod));
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

      return $this->redirect($this->generateUrl('hlp_nebula_mod', array('owner' => $owner->getNameCanonical(), 'mod' => $mod->getModId())));
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

    $form = $this->createForm(new FSModType(), $mod);

    if ($form->handleRequest($request)->isValid()) {
      //$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      $em = $this->getDoctrine()->getManager();

      $em->flush();
      
      $request->getSession()->getFlashBag()->add('success', "Mod <strong>".$mod->getTitle()."</strong> successfully edited.");

      return $this->redirect($this->generateUrl('hlp_nebula_owner', array('owner' => $owner->getNameCanonical())));
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:edit_mod.html.twig', array(
      'owner' => $owner,
      'mod' => $mod,
      'form' => $form->createView()
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
