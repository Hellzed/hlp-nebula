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


namespace HLP\NebulaBundle\Entity;

interface OwnerInterface
{
    public function getId();
    public function getName();
    public function getNameCanonical();
    
    public function addMod(\HLP\NebulaBundle\Entity\FSMod $mods);
    public function removeMod(\HLP\NebulaBundle\Entity\FSMod $mods);
    public function getMods();
    
    public function getNbMods();
    public function getNbBranches();
    public function getNbBuilds();
}

