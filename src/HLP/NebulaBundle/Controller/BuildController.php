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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;


use HLP\NebulaBundle\Entity\FSMod;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Entity\Build;
use HLP\NebulaBundle\Form\BuildType;
use HLP\NebulaBundle\Form\BuildTransferType;

class BuildController extends Controller
{
  public function indexAction($owner, $mod, $branch, $build)
  {
    return $this->redirect($this->generateUrl('hlp_nebula_build_data', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build
    )), 301);
  }
  
  public function showAction(Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    $jsonBuilder = $this->container
                        ->get('hlpnebula.json_builder');
                        
    $data = $jsonBuilder->createFromBuild($build);
    //$data = Array('mods' => Array($data));
    
    return $this->render('HLPNebulaBundle:AdvancedUI:build_show.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build,
      'data'   => $data
    ));
  }
  
  public function showFinalisedAction(Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
                        
    $data = json_encode(json_decode($build->getGeneratedJSON()), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    //$data = Array('mods' => Array($data));
    
    return $this->render('HLPNebulaBundle:AdvancedUI:build_show_finalised.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build,
      'data'   => $data
    ));
  }
  
  public function rawAction(Build $build)
  {
  
    if (null == $build->getGeneratedJSON() && $build->getIsFailed()) {
        throw new NotFoundHttpException("No JSON data for this build, validation failed.");
    }
    
    if (null == $build->getGeneratedJSON() && !$build->getIsFailed()) {
        throw new NotFoundHttpException("No JSON data for this build, validation not started.");
    }
    
    $response = new Response($build->getGeneratedJSON());
    $response->headers
             ->set('Content-Type', 'application/json');
    
    return $response;
  }
  
  public function newBuildFromAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $newBuild = clone $build;
    
    $form = $this->createForm(new BuildType(), $newBuild);
    
    $form->handleRequest($request);

    if ($form->isValid())
    {
      $em = $this->getDoctrine()
                 ->getManager();
                 
      $em->persist($newBuild);
      $em->flush();
      
      $request->getSession()
              ->getFlashBag()
              ->add('success', "New build <strong>version ".$newBuild->getVersion()."</strong> successfully created from <strong>version ".$build->getVersion()."</strong>.");

      return $this->redirect($this->generateUrl('hlp_nebula_process', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch,
        'build'  => $newBuild
      )));
    }
    
    if ((!$form->isValid()) && $request->isMethod('POST') )
    {
      $request->getSession()
              ->getFlashBag()
              ->add('error', '<strong>Invalid data !</strong> Please check this form again.');
    }
    
    return $this->render('HLPNebulaBundle:AdvancedUI:copy_and_update_build.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build,
      'form'   => $form->createView()
    ));
  }
  
  public function transferAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    $newBuild = clone $build;
    
    $form = $this->createForm(new BuildTransferType(), $newBuild);
    
    if ($form->handleRequest($request)->isValid())
    {
      
      $em = $this->getDoctrine()
                 ->getManager();
                 
      $em->persist($newBuild);
      $em->flush();
      
      $request->getSession()
              ->getFlashBag()
              ->add('success', "New build <strong>version ".$newBuild->getVersion()."</strong> successfully created from <strong>version ".$build->getVersion()."</strong>.");

      return $this->redirect($this->generateUrl('hlp_nebula_process', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $newBuild->getBranch(),
        'build'  => $newBuild
      )));
    }
    
    if ((!$form->isValid()) && $request->isMethod('POST') )
    {
      $request->getSession()
              ->getFlashBag()
              ->add('error', '<strong>Invalid data !</strong> Please check this form again.');
    }
    
    return $this->render('HLPNebulaBundle:AdvancedUI:transfer_build.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build,
      'form'   => $form->createView()
    ));
  }
  
  public function processAction(Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    if(($build->getIsFailed() == false) && ($build->getIsReady() == false))
    {
      if($build->getConverterTicket() == null)
      {
        $jsonBuilder = $this->container
                            ->get('hlpnebula.json_builder');
        
        $data = $jsonBuilder->createFromBuild($build, false);
        $data = json_encode(Array('mods' => Array($data)));
        
        $ks = $this->container
                   ->get('hlpnebula.knossos_server_connect');
                   
        $webhook = $this->generateUrl('hlp_nebula_process_finalise', array(
            'owner'  => $owner,
            'mod'    => $mod,
            'branch' => $branch,
            'build'  => $build
        ), true);
        
        $ksresponse = json_decode($ks->sendData($data, $webhook));
        
        
        if($ksresponse)
        {
          $build->setConverterToken($ksresponse->token);
          $build->setConverterTicket($ksresponse->ticket);
          
          $em = $this->getDoctrine()
                     ->getManager();
                     
          $em->flush();
        }
        else
        {
          return $this->render('HLPNebulaBundle:AdvancedUI:process_error.html.twig', array(
            'owner'  => $owner,
            'mod'    => $mod,
            'branch' => $branch,
            'build'  => $build,
          ));
        }
      }
      
      $ksticket = $build->getConverterTicket();
      
      return $this->render('HLPNebulaBundle:AdvancedUI:process_build.html.twig', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch,
        'build'  => $build,
        'ksticket' => $ksticket
      ));
    }
    
    return $this->redirect($this->generateUrl('hlp_nebula_build', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch,
        'build'  => $build
    )));
  }
  
  public function processFinaliseAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if(($build->getIsFailed() == false) && ($build->getIsReady() == false))
    {
      if($build->getConverterTicket() != null)
      {
        $ks = $this->container
                   ->get('hlpnebula.knossos_server_connect');
        
        $ksresponse = json_decode($ks->retrieveData($build->getConverterTicket(), $build->getConverterToken()));
        
        if($ksresponse)
        {
          if($ksresponse->finished == true)
          {
            if($ksresponse->success == true)
            {
              $build->setGeneratedJSON($ksresponse->json);
              $build->setIsReady(true);
              $request->getSession()
                      ->getFlashBag()
                      ->add('success', "Build <strong>version ".$build->getVersion()."</strong> has been successfully validated.");
            }
            else
            {
              $build->setIsFailed(true);
              $request->getSession()
                      ->getFlashBag()
                      ->add('warning', "Build <strong>version ".$build->getVersion()."</strong> validation has failed.");
            }
            
            $em = $this->getDoctrine()
                       ->getManager();
                       
            $em->flush();
          }
          else
          {
            return $this->redirect($this->generateUrl('hlp_nebula_process', array(
                'owner'  => $owner,
                'mod'    => $mod,
                'branch' => $branch,
                'build'  => $build
            )));
          }
        }
        else
        {
          return $this->render('HLPNebulaBundle:AdvancedUI:process_error.html.twig', array(
            'owner'  => $owner,
            'mod'    => $mod,
            'branch' => $branch,
            'build'  => $build,
          ));
        }
      }
    }
    
    if($request->getMethod() == 'POST')
    {      
      $response = new Response(json_encode(array('cancelled' => $build->getIsFailed())));
      $response->headers
               ->set('Content-Type', 'application/json');
               
      return $response;
    }
    
    return $this->redirect($this->generateUrl('hlp_nebula_build', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch,
        'build'  => $build
    )));
  }
  
  public function processForceFailAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }
    
    if(($build->getIsFailed() == false) && ($build->getIsReady() == false))
    {
      $build->setIsFailed(true);
      
      $em = $this->getDoctrine()
                 ->getManager();
                   
      $em->flush();
      
      $request->getSession()
              ->getFlashBag()
              ->add('warning', "Build <strong>version ".$build->getVersion()."</strong> has been marked as failed. Processing canceled.");
              
    }
    
    return $this->redirect($this->generateUrl('hlp_nebula_build', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch,
        'build'  => $build
    )));
  }
  
  public function deleteAction(Request $request, Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();

    if (false === $this->get('security.context')->isGranted('add', $owner)) {
        throw new AccessDeniedException('Unauthorised access!');
    }

    $form = $this->createFormBuilder()
                 ->getForm();

    if ($form->handleRequest($request)->isValid())
    {
      $em = $this->getDoctrine()
                 ->getManager();
                 
      $em->remove($build);
      $em->flush();

      $request->getSession()
              ->getFlashBag()
              ->add('success', "Build <strong>version ".$build->getVersion()."</strong> has been deleted.");

      return $this->redirect($this->generateUrl('hlp_nebula_branch', array(
        'owner'  => $owner,
        'mod'    => $mod,
        'branch' => $branch
      )));
    }

    return $this->render('HLPNebulaBundle:AdvancedUI:delete_build.html.twig', array(
      'owner'  => $owner,
      'mod'    => $mod,
      'branch' => $branch,
      'build'  => $build,
      'form'   => $form->createView()
    ));
  }

}
