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

class BaseController extends Controller
{
  public function indexAction()
  {
    return $this->render('HLPNebulaBundle:AdvancedUI:homepage.html.twig');
  }
  
  public function getStartedAction()
  {
    return $this->render('HLPNebulaBundle:AdvancedUI:get_started.html.twig');
  }
  
  public function modsAction()
  {
    return $this->render('HLPNebulaBundle:AdvancedUI:all_mods.html.twig');
  }
  
  public function moddersAction()
  {
    return $this->render('HLPNebulaBundle:AdvancedUI:all_modders.html.twig');
  }
  
  public function activityAction()
  {
    return $this->render('HLPNebulaBundle:AdvancedUI:all_activity.html.twig');
  }
  
}
