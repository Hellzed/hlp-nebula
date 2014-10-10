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

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use HLP\NebulaBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    $names = array(
      'Essentials',
      'New',
      'Highlights',
      'Classics',
      'Pre-Great War Era',
      'Great War',
      'Reconstruction Era',
      'Second Incursion',
      'Post-Second Incursion Era',
      'Alternate timelines & parallel universes',
      'Parodies',
      'Total conversions'
    );

    foreach ($names as $name) {
      $category = new Category();
      $category->setName($name);

      $manager->persist($category);
    }

    $manager->flush();
  }
}
