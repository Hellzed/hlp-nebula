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

// src/HLP/NebulaBundle/JSONBuilder/JSONBuilder.php

namespace HLP\NebulaBundle\JSONBuilder;

class JSONBuilder
{
  /**
   * @param object $build
   * @return array
   */
  public function createFromBuild(\HLP\NebulaBundle\Entity\Build $build)
  {
    $branch = $build->getBranch();
    $mod = $branch->getMod();
    $owner = $mod->getOwner();
    
    $data = Array();
    $data['id'] = $mod->getModId();
    $data['title'] = $mod->getTitle();
    $data['version'] = $build->getVersion();
    
    if($mod->getDescription()) {
      $data['description'] = $mod->getDescription();
    }
    
    if($build->getNotes() || $branch->getNotes() || $mod->getNotes()) {
      $data['notes'] = 'MOD NOTES :\n'.$mod->getNotes().'\n\nBRANCH NOTES :\n'.$branch->getNotes().'\n\nBUILD NOTES :\n'.$build->getNotes();
    }
    
    if($build->getFolder()) {
      $data['folder'] = $build->getFolder();
    }
    
    if(count($build->getPackages()) > 0) {
      $data['packages'] = Array();
      
      foreach ($build->getPackages() as $i => $package) {
        $data['packages'][$i] = Array();
        $data['packages'][$i]['name'] = $package->getName();
        
        if($package->getNotes()) {
          $data['packages'][$i]['notes'] = $package->getNotes();
        }
        
        $data['packages'][$i]['status'] = $package->getStatus();
        
        if(count($package->getDependencies()) > 0) {
          $data['packages'][$i]['dependencies'] = Array();
          
          foreach ($package->getDependencies() as $j => $dependency) {
            $data['packages'][$i]['dependencies'][$j] = Array();
            $data['packages'][$i]['dependencies'][$j]['id'] = $dependency->getDepId();
            $data['packages'][$i]['dependencies'][$j]['version'] = $dependency->getVersion();
            
            if(count($dependency->getDepPkgs()) > 0) {
              $data['packages'][$i]['dependencies'][$j]['packages'] = Array();
              
              foreach ($dependency->getDepPkgs() as $k => $pkg) {
                $data['packages'][$i]['dependencies'][$j]['packages'][$k] = $pkg;
              }
            }
          }
        }
        
        if(count($package->getEnvVars()) > 0) {
          $data['packages'][$i]['environment'] = Array();
          
          foreach ($package->getEnvVars() as $j => $envVar) {
            $data['packages'][$i]['environment'][$j] = Array();
            $data['packages'][$i]['environment'][$j]['type'] = $envVar->getType();
            $data['packages'][$i]['environment'][$j]['value'] = $envVar->getValue();
          }
        }
        
        if(count($package->getFiles()) > 0) {
          $data['packages'][$i]['files'] = Array();
          
          foreach ($package->getFiles() as $j => $file) {
            $data['packages'][$i]['files'][$j] = Array();
            $data['packages'][$i]['files'][$j]['filename'] = $file->getFilename();
            $data['packages'][$i]['files'][$j]['is_archive'] = $file->getIsArchive();
            
            if($file->getDest()) {
              $data['packages'][$i]['files'][$j]['dest'] = $file->getDest();
            }

            $data['packages'][$i]['files'][$j]['urls'] = $file->getUrls();
            
            if(count($file->getUrls()) > 0) {
              $data['packages'][$i]['files'][$j]['urls'] = Array();
              
              foreach ($file->getUrls() as $k => $url) {
                $data['packages'][$i]['files'][$j]['urls'][$k] = $url;
              }
            }
          }
        }
      }
    }
    
    if(count($build->getActions()) > 0) {
      $data['actions'] = Array();
      
      foreach ($build->getActions() as $i => $action) {
        $data['actions'][$i] = Array();
        $data['actions'][$i]['type'] = $action->getType();
        $data['actions'][$i]['paths'] = $action->getPaths();
        
        if('move' == $action->getType()) {
          $data['actions'][$i]['dest'] = $action->getDest();
        }
        
        $data['actions'][$i]['glob'] = $action->getGlob();
      }
    }
    
    return $data;
  }
}
