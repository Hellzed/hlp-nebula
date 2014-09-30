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
use Symfony\Component\HttpFoundation\JsonResponse;

use HLP\NebulaBundle\Entity\FSMod;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Entity\Build;
use HLP\NebulaBundle\Entity\Package;

class AJAXController extends Controller
{
  public function searchModsAction(Request $request)
  {
    if($request->isXmlHttpRequest())
    {

      $em = $this->getDoctrine()->getManager();
      $term = '';
      $term = $request->query->get('term');
      if($term != '')
      {
        $qb = $em->createQueryBuilder();

        $qb->select('m')
          ->from('HLPNebulaBundle:FSMod', 'm')
          ->where("m.modId LIKE :keyword")
          ->orderBy('m.modId', 'ASC')
          ->setParameter('keyword', '%'.$term.'%');

        $query = $qb->getQuery();               
        $mods = $query->getResult();
      }
      else {
        $mods = null;
      }
      
      $data = Array();
      foreach($mods as $mod)
      {
        $data[] = $mod->getModId();
      }
      
      $response = new JsonResponse();
      $response->setData($data);
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }
    else
    {
      return $this->redirect($this->generateUrl('hlp_nebula_homepage'));
    }
  }
  
  public function searchPacakgesInModAction(Request $request, $mod)
  {
    if($request->isXmlHttpRequest())
    {

      $em = $this->getDoctrine()->getManager();
      $term = '';
      $term = $request->query->get('term');
      
      
      $qb = $em->createQueryBuilder();

      $qb->from('HLPNebulaBundle:Package', 'p')
          ->select('p')
          ->leftJoin('p.build', 'u')
          ->leftJoin('u.branch', 'b')
          ->leftJoin('b.mod', 'm')
          ->where("m.modId = :modId")
          ->orderBy('p.name', 'ASC')
          ->setParameter('modId', $mod);
 
      if($term != '')
      {
      
      $qb->andWhere("p.name LIKE :keyword")
         ->setParameter('keyword', '%'.$term.'%');
      }
      
      $query = $qb->getQuery();               
      $packages = $query->getResult();
      
      $data = Array();
      foreach($packages as $package)
      {
        $data[] = $package->getName();
      }
      
      $response = new JsonResponse();
      $response->setData(array_unique($data));
      $response->headers->set('Content-Type', 'application/json');

      return $response;
    }
    else
    {
      return $this->redirect($this->generateUrl('hlp_nebula_homepage'));
    }
  }
}
