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
use HLP\NebulaBundle\Entity\Build;

class BuildController extends Controller
{
  public function indexAction($owner, $mod, $branch, $build)
  {
    return $this->redirect($this->generateUrl('hlp_nebula_build_data', array('owner' => $owner, 'mod' => $mod, 'branch' => $branch, 'build' => $build)), 301);
  }
  
  public function showAction(Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    $jsonBuilder = $this->container->get('hlpnebula.json_builder');
    $data = $jsonBuilder->createFromBuild($build);
    
    return $this->render('HLPNebulaBundle:AdvancedUI:build.html.twig', array('owner' => $owner, 'mod' => $mod, 'branch' => $branch, 'build' => $build, 'data' => $data));
  }
  
  public function deleteAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();

    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }

    $form = $this->createFormBuilder()->getForm();

    if ($form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->remove($build);
      $em->flush();

      $request->getSession()->getFlashBag()->add('success', "Build <strong>version ".$build->getVersion()."</strong> has been deleted.");

      return $this->redirect($this->generateUrl('hlp_nebula_branch', array('owner' => $owner->getNameCanonical(), 'mod' => $mod->getModId(), 'branch' => $branch->getBranchId())));
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('HLPNebulaBundle:AdvancedUI:delete_build.html.twig', array(
      'owner' => $owner,
      'mod' => $mod,
      'branch' => $branch,
      'build' => $build,
      'form' => $form->createView()
    ));
  }
}
