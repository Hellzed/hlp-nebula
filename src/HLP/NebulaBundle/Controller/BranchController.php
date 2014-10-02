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
use HLP\NebulaBundle\Entity\Build;
use HLP\NebulaBundle\Form\BranchEditType;
use HLP\NebulaBundle\Form\BuildType;


class BranchController extends Controller
{
  public function indexAction($owner, $mod, $branch)
  {
    return $this->redirect($this->generateUrl('hlp_nebula_branch_builds', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch
    )), 301);
  }
  
  //IMPLICIT : string to Branch parameter conversion, with custom ParamConverter
  //Keeping this param converter config as a reference. I'll use this to optimise requests later.
  /**
   * @ParamConverter("branch", options={"test": false})
   */
  public function buildsAction(Branch $branch, $page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("Page ".$page." does not exist.");
    }
    
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    $buildsPerPage = 10;
    $buildsAll = $branch->getBuilds()->toArray();
    $nbPages = ceil(count($buildsAll)/$buildsPerPage);
    
    if (($page > $nbPages) && ($page > 1)) {
      throw $this->createNotFoundException("Page ".$page." does not exist.");
    }
    
    $buildsList = array_slice($buildsAll, ($page-1)*$buildsPerPage, $buildsPerPage);
    
    return $this->render('HLPNebulaBundle:AdvancedUI:branch_builds.html.twig', array(
      'owner'      => $owner,
      'mod'        => $mod,
      'branch'     => $branch,
      'buildsList' => $buildsList,
      'page'       => $page,
      'nbPages'    => $nbPages
    ));
  }
  
  public function detailsAction(Branch $branch)
  {
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    $session = new Session();
    $session->set('branchRefer', 'fromDetails');
    
    return $this->render('HLPNebulaBundle:AdvancedUI:branch_details.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch
    ));
  }
  
  public function activityAction(Branch $branch)
  {
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    return $this->render('HLPNebulaBundle:AdvancedUI:branch_activity.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch
    ));
  }
  
  public function newBuildAction(Request $request, Branch $branch)
  {
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $build = new Build;
    $build->setBranch($branch);
    
    $form = $this->createForm(new BuildType(), $build);

    $form->handleRequest($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()
                 ->getManager();
                 
      $em->persist($build);
      $em->flush();
      
      $request->getSession()
              ->getFlashBag()
              ->add('success', 'New build <strong>version '.$build->getVersion().'</strong> successfully created.');

      return $this->redirect($this->generateUrl('hlp_nebula_branch', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch
      )));
    }
    
    if ((!$form->isValid()) && $request->isMethod('POST') )
    {
      $request->getSession()
              ->getFlashBag()
              ->add('error', '<strong>Invalid data !</strong> Please check this form again.');
    }
    
    return $this->render('HLPNebulaBundle:AdvancedUI:new_build.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'form'   => $form->createView()
    ));
  }
  
  public function editAction(Request $request, Branch $branch)
  {
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $session = new Session();
    $refer = $session->get('branchRefer');
    $referURL = $this->getReferURL($refer, $owner, $mod, $branch);

    $form = $this->createForm(new BranchEditType(), $branch);

    if ($form->handleRequest($request)->isValid()) {
      
      $em = $this->getDoctrine()
                 ->getManager();

      $em->flush();
      
      $request->getSession()
              ->getFlashBag()
              ->add('success', 'Branch <strong>"'.$branch->getName().'" (id: '.$branch->getBranchId().')</strong> has been successfully edited.');

      return $this->redirect($referURL);
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:edit_branch.html.twig', array(
      'owner'    => $owner,
      'mod'      => $mod,
      'branch'   => $branch,
      'form'     => $form->createView(),
      'referURL' => $referURL
    ));
  }
  
  public function deleteAction(Request $request, Branch $branch)
  {
    $mod = $branch->getMod();
    $owner = $mod->getOwner();

    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $session = new Session();
    $refer = $session->get('branchRefer');
    $referURL = $this->getReferURL($refer, $owner, $mod, $branch);

    $form = $this->createFormBuilder()
                 ->getForm();

    if ($form->handleRequest($request)->isValid())
    {
      $em = $this->getDoctrine()
                 ->getManager();
                 
      $em->remove($branch);
      $em->flush();

      $request->getSession()
              ->getFlashBag()
              ->add('success', 'Branch <strong>"'.$branch->getName().'" (id: '.$branch->getBranchId().')</strong> has been deleted.');
      
      return $this->redirect($this->generateUrl('hlp_nebula_mod', array(
        'mod'   => $mod,
        'owner' => $owner
      )));
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:delete_branch.html.twig', array(
      'owner'    => $owner,
      'mod'      => $mod,
      'branch'   => $branch,
      'form'     => $form->createView(),
      'referURL' => $referURL
    ));
  }
  
  private function getReferURL($refer, $owner, $mod, $branch)
  {
    if($refer == 'fromDetails')
    {
      $referURL = $this->generateUrl('hlp_nebula_branch_details', array(
        'branch' => $branch,
        'mod'    => $mod,
        'owner'  => $owner
      ));
    }
    else
    {
      $referURL = $this->generateUrl('hlp_nebula_mod_branches', array(
        'mod'   => $mod,
        'owner' => $owner
      ));
    }
    
    return $referURL;
  }
}
