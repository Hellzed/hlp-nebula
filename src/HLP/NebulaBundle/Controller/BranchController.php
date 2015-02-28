<?php

/*
* Copyright 2014 HLP-Nebula authors, see NOTICE file
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use HLP\NebulaBundle\Entity\Meta;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Form\BranchType;
use HLP\NebulaBundle\Form\BranchEditType;


class BranchController extends Controller
{
    /**
     * @ParamConverter("branch", options={"mapping": {"meta": "meta", "branch": "branchId"}, "repository_method" = "findOneWithParent"})
     */
    public function showDetailsAction(Request $request, Branch $branch)
    {
        $session = new Session();
        $session->set('branch_refer', $this->getRequest()
                                           ->getUri()
        );
        
        return $this->render('HLPNebulaBundle:Branch:details.html.twig', array(
            'meta'   => $branch->getMeta(),
            'branch' => $branch
        ));
    }
    
    /**
     * @ParamConverter("branch", options={"mapping": {"meta": "meta", "branch": "branchId"}, "repository_method" = "findOneWithParent"})
     */
    public function showBuildsAction(Request $request, Branch $branch, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $nbPerPage = 10;
        
        $buildsList = $this->getDoctrine()
            ->getManager()
            ->getRepository('HLPNebulaBundle:Build')
            ->getBuilds($branch, $page, $nbPerPage)
        ;

        $nbPages = ceil(count($buildsList)/$nbPerPage);
        
        if ($page > $nbPages && $nbPages != 0) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $session = new Session();
        $session->set('build_refer', $this->getRequest()
                                           ->getUri()
        );
    
        return $this->render('HLPNebulaBundle:Branch:builds.html.twig', array(
            'meta'   => $branch->getMeta(),
            'branch' => $branch,
            'buildsList'   => $buildsList,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }
    
    /**
     * @ParamConverter("branch", options={"mapping": {"meta": "meta", "branch": "branchId"}, "repository_method" = "findOneWithParent"})
     */
    public function showActivityAction(Branch $branch)
    {
        return $this->render('HLPNebulaBundle:Branch:activity.html.twig', array(
            'meta'   => $branch->getMeta(),
            'branch' => $branch
        ));
    }
    
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     * @Security("is_granted('EDIT', meta)")
     */
    public function createAction(Request $request, Meta $meta)
    {
        $branch = new Branch;
        
        $form = $this->createForm(new BranchType(), $branch);

        if ($form->handleRequest($request)->isValid())
        {
            if ($meta->getNbBranches() == 0) {
                $branch->setIsDefault(true);
            }
            
            $meta->addBranch($branch);
            
            $em = $this->getDoctrine()
                ->getManager();

            $em->persist($branch);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'New branch <strong>"'.$branch->getName().'" (id: '.$branch->getBranchId().')</strong> successfully created.');

            return $this->redirect($this->generateUrl('hlp_nebula_repository_branch', array(
                'meta'      => $meta,
                'branch'    => $branch
            )));
        }

        return $this->render('HLPNebulaBundle:Branch:create.html.twig', array(
            'meta'  => $meta,
            'form'  => $form->createView()
        ));
    }
    
    /**
     * @ParamConverter("branch", options={"mapping": {"meta": "meta", "branch": "branchId"}, "repository_method" = "findOneWithParent"})
     * @Security("is_granted('EDIT', branch.getMeta())")
     */
    public function updateAction(Request $request, Branch $branch)
    {
        $session = new Session();
        $referURL = $session->get('branch_refer');

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

        return $this->render('HLPNebulaBundle:Branch:update.html.twig', array(
            'meta'     => $branch->getMeta(),
            'branch'   => $branch,
            'form'     => $form->createView(),
            'referURL' => $referURL
        ));
    }
  
    /**
     * @ParamConverter("branch", options={"mapping": {"meta": "meta", "branch": "branchId"}, "repository_method" = "findOneWithParent"})
     * @Security("is_granted('EDIT', branch.getMeta())")
     */
    public function deleteAction(Request $request, Branch $branch)
    {
        $session = new Session();
        $referURL = $session->get('branch_refer');

        $form = $this->createFormBuilder()
            ->getForm();

        if ($form->handleRequest($request)->isValid()) {
            $branch->getMeta()->removeBranch($branch);
            
            $em = $this->getDoctrine()
                ->getManager();

            $em->remove($branch);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Branch <strong>"'.$branch->getName().'" (id: '.$branch->getBranchId().')</strong> has been deleted.');

            return $this->redirect($this->generateUrl('hlp_nebula_repository_meta', array(
                'meta'   => $branch->getMeta()
            )));
        }

        return $this->render('HLPNebulaBundle:Branch:delete.html.twig', array(
            'meta'      => $branch->getMeta(),
            'branch'    => $branch,
            'form'      => $form->createView(),
            'referURL'  => $referURL
        ));
    }
}
