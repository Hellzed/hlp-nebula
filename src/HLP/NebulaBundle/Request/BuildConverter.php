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

namespace HLP\NebulaBundle\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;

class BuildConverter implements ParamConverterInterface
{
  protected $class;
  protected $repository;

  public function __construct($class, EntityManager $em)
  {
    $this->class = $class;
    $this->repository = $em->getRepository($class);
  }

  public function apply(Request $request, ParamConverter $configuration)
  {
    //$options = $this->getOptions($configuration);
    //echo $options['test'];
    $ownerNameCanonical = $request->attributes->get('owner');
    $modId = $request->attributes->get('mod');
    $branchId = $request->attributes->get('branch');
    
    if($branchId == 'default')
    {
      $branchId = null;
    }
    
    $version = $request->attributes->get('build');
    
    if(!($version == 'current'))
    {
      list($versionRest, $versionMetadata) = array_pad(explode('+', $version, 2), 2, null);
      list($versionMain, $versionPreRelease) = array_pad(explode('-', $versionRest, 2), 2, null);
      list($versionMajor, $versionMinor, $versionPatch) = explode('.', $versionMain);
    }
    else
    {
      $versionMajor = null;
      $versionMinor = null;
      $versionPatch = null;
      $versionPreRelease = null;
      $versionMetadata = null;
    }
    
    $build = $this->repository->findSingleBuild($ownerNameCanonical, $modId, $branchId, $versionMajor, $versionMinor, $versionPatch, $versionPreRelease, $versionMetadata);
    
    if (null === $build) {
        throw new NotFoundHttpException("Build not found.");
    }

    $request->attributes->set($configuration->getName(), $build);
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
