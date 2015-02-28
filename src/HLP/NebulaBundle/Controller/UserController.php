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
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use HLP\NebulaBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     */
    public function showProfileAction(User $user)
    {
        return $this->render('HLPNebulaBundle:User:profile.html.twig', array(
            'user'   => $user
        ));
    }
    
    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     */
    public function showMetasAction(Request $request, User $user, $page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $nbPerPage = 10;
        
        $metasList = $this->getDoctrine()
            ->getManager()
            ->getRepository('HLPNebulaBundle:Meta')
            ->getMetas($user, $page, $nbPerPage)
        ;

        $nbPages = ceil(count($metasList)/$nbPerPage);
        
        if ($page > $nbPages && $nbPages != 0) {
            throw $this->createNotFoundException("Page ".$page." not found.");
        }
        
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentities = array();
        foreach ($metasList as $meta) {
            $objectIdentities[] = ObjectIdentity::fromDomainObject($meta);
        }
        
        $aclProvider->findAcls($objectIdentities);
        
        $session = new Session();
        $session->set('meta_refer', $this->getRequest()
                                         ->getUri()
        );
        
        return $this->render('HLPNebulaBundle:User:metas.html.twig', array(
            'user'  => $user,
            'metasList' => $metasList,
            'nbPages' => $nbPages,
            'page' => $page
        ));
    }
    
    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     */
    public function showActivityAction(User $user)
    {
        return $this->render('HLPNebulaBundle:User:activity.html.twig', array(
            'user'  => $user
        ));
    }
}
