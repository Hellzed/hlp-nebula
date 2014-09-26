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

namespace HLP\NebulaBundle\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;

class OwnerConverter implements ParamConverterInterface
{
  protected $class;
  protected $repository;

  public function __construct($class, EntityManager $em)
  {
    $this->class = $class;
    $this->repository = $em->getRepository('HLP\NebulaBundle\Entity\User');
  }

  public function apply(Request $request, ParamConverter $configuration)
  {
    //find with joins À FAIRE !!!
    //+logique de séléction mod>branch>build
    //$options = $this->getOptions($configuration);
    //echo $options['test'];
    $modderId = $request->attributes->get('owner');
    $modder = $this->repository->findSingleUser($modderId); 

    if (null === $modder) {
        throw new NotFoundHttpException("Modder or modding team not found.");
    }
    
    $request->attributes->set($configuration->getName(), $modder);
    return true;
  }

  public function supports(ParamConverter $configuration)
  {
    return $this->class === $configuration->getClass();
  }
  
  protected function getOptions(ParamConverter $configuration)
  {
    return array_replace(array(
    'test' => false,
    ), $configuration->getOptions());
  }
}
