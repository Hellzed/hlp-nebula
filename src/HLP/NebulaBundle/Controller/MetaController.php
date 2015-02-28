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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

use HLP\NebulaBundle\Entity\Meta;
use HLP\NebulaBundle\Entity\Branch;
use HLP\NebulaBundle\Form\MetaType;
use HLP\NebulaBundle\Form\MetaEditType;
use HLP\NebulaBundle\Form\BranchType;

class MetaController extends Controller
{
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     */
    public function showDetailsAction(Request $request, Meta $meta)
    {
        $session = new Session();
        $session->set('meta_refer', $this->getRequest()
                                         ->getUri()
        );
      
        return $this->render('HLPNebulaBundle:Meta:details.html.twig', array(
            'meta'   => $meta
        ));
    }
    
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     */
    public function showBranchesAction(Request $request, Meta $meta, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $nbPerPage = 10;
        
        $branchesList = $this->getDoctrine()
            ->getManager()
            ->getRepository('HLPNebulaBundle:Branch')
            ->getBranches($meta, $page, $nbPerPage)
        ;

        $nbPages = ceil(count($branchesList)/$nbPerPage);
        
        if ($page > $nbPages && $nbPages != 0) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $session = new Session();
        $session->set('branch_refer', $this->getRequest()
                                           ->getUri()
        );
    
        return $this->render('HLPNebulaBundle:Meta:branches.html.twig', array(
            'meta'   => $meta,
            'branchesList'   => $branchesList,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }
    
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     */
    public function showTeamAction(Meta $meta, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $nbPerPage = 10;
        
        $usersList = $this->getDoctrine()
            ->getManager()
            ->getRepository('HLPNebulaBundle:User')
            ->getUsers($meta, $page, $nbPerPage)
        ;

        $nbPages = ceil(count($usersList)/$nbPerPage);
        
        if ($page > $nbPages && $nbPages != 0) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        return $this->render('HLPNebulaBundle:Meta:team.html.twig', array(
            'meta'  => $meta,
            'usersList' => $usersList,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }
    
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     */
    public function showActivityAction(Meta $meta)
    {
        return $this->render('HLPNebulaBundle:Meta:activity.html.twig', array(
            'meta'   => $meta
        ));
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function createAction(Request $request)
    {
        $meta = new Meta;
        
        $form = $this->createForm(new MetaType(), $meta);
        
        if ($form->handleRequest($request)->isValid())
        {
            $defaultBranch = new Branch;
            $defaultBranch->setBranchId("master");
            $defaultBranch->setName("Master");
            $defaultBranch->setNotes("This is a default branch, created automatically on mod creation.");
            $defaultBranch->setIsDefault(true);
            
            $meta->addBranch($defaultBranch);
            $meta->addUser($this->getUser());

            $em = $this->getDoctrine()
                ->getManager();
                     
            $em->persist($meta);
            $em->persist($defaultBranch);

            $em->flush();

            $request->getSession()
                  ->getFlashBag()
                  ->add('success', 'New mod <strong>"'.$meta->getTitle().'" (id: '.$meta->getMetaId().')</strong> successfully created.<br/><hr/>A default branch has been created for this mod : <strong>"'.$defaultBranch->getName().'" (id: '.$defaultBranch->getBranchId().')</strong>.');
                  
            // création de l'ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($meta);
            $acl = $aclProvider->createAcl($objectIdentity);

            // retrouve l'identifiant de sécurité de l'utilisateur actuellement connecté
            $user = $this->getUser();

            // donne accès au propriétaire
            $securityIdentity = UserSecurityIdentity::fromAccount($user);
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);


            $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OPERATOR);

            $aclProvider->updateAcl($acl);
            
            return $this->redirect($this->generateUrl('hlp_nebula_repository_branch', array(
                'meta'    => $meta,
                'branch'  => $defaultBranch
            )));
        }
        
        return $this->render('HLPNebulaBundle:Meta:create.html.twig', array(
            'form'  => $form->createView()
        ));
    }
    
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     * @Security("is_granted('EDIT', meta)")
     */
    public function updateAction(Request $request, Meta $meta)
    {
        $session = new Session();
        $referURL = $session->get('meta_refer');

        $form = $this->createForm(new MetaEditType(), $meta);

        if ($form->handleRequest($request)->isValid()) {
            foreach ($meta->getBranches() as $branch) {
                $branch->setIsDefault(false);
            }

            $defaultBranch = $form->get('defaultBranch')->getData();

            if ($defaultBranch) {
                $defaultBranch->setIsDefault(true);
            }

            $em = $this->getDoctrine()
                ->getManager();

            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Mod <strong>"'.$meta->getTitle().'" (id: '.$meta->getMetaId().')</strong> has been successfully edited.');

            return $this->redirect($referURL);
        }

        return $this->render('HLPNebulaBundle:Meta:update.html.twig', array(
            'meta'      => $meta,
            'form'     => $form->createView(),
            'referURL' => $referURL
        ));
    }
  
    /**
     * @ParamConverter("meta", options={"mapping": {"meta": "metaId"}})
     * @Security("is_granted('DELETE', meta)")
     */
    public function deleteAction(Request $request, Meta $meta)
    {  
        $session = new Session();
        $referURL = $session->get('meta_refer');

        $form = $this->createFormBuilder()->getForm();

        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()
                ->getManager();
               
            $em->remove($meta);
            $em->flush();

            $request->getSession()
                ->getFlashBag()
                ->add('success', 'Mod <strong>"'.$meta->getTitle().'" (id: '.$meta->getMetaId().')</strong> has been deleted.');

            return $this->redirect($this->generateUrl('hlp_nebula_user', array('user' => $this->getUser())));
        }

        return $this->render('HLPNebulaBundle:Meta:delete.html.twig', array(
            'meta'      => $meta,
            'form'     => $form->createView(),
            'referURL' => $referURL
        ));
    }
}
