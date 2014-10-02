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

namespace HLP\NebulaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Package
 *
 * @ORM\Table(name="hlp_nebula_package")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\PackageRepository")
 */
class Package
{
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\EnvVar", mappedBy="package", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $envVars;
    
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Dependency", mappedBy="package", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $dependencies;
    
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\File", mappedBy="package", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $files;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Build", inversedBy="packages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $build;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[-\w]+$/", message="Special characters not allowed in the package name.")
     * @Assert\Regex("/^-/", match=false, message="Dash not allowed at the beginning of the package name.")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Package
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Package
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Package
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set build
     *
     * @param \HLP\NebulaBundle\Entity\Build $build
     * @return Package
     */
    public function setBuild(\HLP\NebulaBundle\Entity\Build $build)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return \HLP\NebulaBundle\Entity\Build 
     */
    public function getBuild()
    {
        return $this->build;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->envVars = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dependencies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __clone()
    {
         if ($this->id) {
            $this->id = null;
            
            $newEnvVars = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($this->envVars as $envVar) {
              $newEnvVar = clone $envVar;
              $newEnvVars[] = $newEnvVar;
              $newEnvVar->setPackage($this);
            }
            $this->envVars = $newEnvVars;
            
            $newDependencies = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($this->dependencies as $dependency) {
              $newDependency = clone $dependency;
              $newDependencies[] = $newDependency;
              $newDependency->setPackage($this);
            }
            $this->dependencies = $newDependencies;
            
            $newFiles = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($this->files as $file) {
              $newFile = clone $file;
              $newFiles[] = $newFile;
              $newFile->setPackage($this);
            }
            $this->files = $newFiles;
         }
    }

    /**
     * Add envVars
     *
     * @param \HLP\NebulaBundle\Entity\EnvVar $envVars
     * @return Package
     */
    public function addEnvVar(\HLP\NebulaBundle\Entity\EnvVar $envVars)
    {
        $this->envVars[] = $envVars;
        $envVars->setPackage($this);
        return $this;
    }

    /**
     * Remove envVars
     *
     * @param \HLP\NebulaBundle\Entity\EnvVar $envVars
     */
    public function removeEnvVar(\HLP\NebulaBundle\Entity\EnvVar $envVars)
    {
        $this->envVars->removeElement($envVars);
    }

    /**
     * Get envVars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnvVars()
    {
        return $this->envVars;
    }

    /**
     * Add dependencies
     *
     * @param \HLP\NebulaBundle\Entity\Dependency $dependencies
     * @return Package
     */
    public function addDependency(\HLP\NebulaBundle\Entity\Dependency $dependencies)
    {
        $this->dependencies[] = $dependencies;
        $dependencies->setPackage($this);
        return $this;
    }

    /**
     * Remove dependencies
     *
     * @param \HLP\NebulaBundle\Entity\Dependency $dependencies
     */
    public function removeDependency(\HLP\NebulaBundle\Entity\Dependency $dependencies)
    {
        $this->dependencies->removeElement($dependencies);
    }

    /**
     * Get dependencies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Add files
     *
     * @param \HLP\NebulaBundle\Entity\File $files
     * @return Package
     */
    public function addFile(\HLP\NebulaBundle\Entity\File $files)
    {
        $this->files[] = $files;
        $files->setPackage($this);
        return $this;
    }

    /**
     * Remove files
     *
     * @param \HLP\NebulaBundle\Entity\File $files
     */
    public function removeFile(\HLP\NebulaBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
    
    /**
     * @Assert\Callback
     */
    public function filenamesUnique(ExecutionContextInterface $context)
    {
      $filenames = Array();
      
      foreach ($this->files as $key => $file) {
        $filenames[$key] = $file->getFilename();
      }
      
      if(count($filenames) !== count(array_unique($filenames))) {
        $context->addViolationAt(
            'files',
            'Duplicated filenames !',
            array(),
            null
            );
        }
    }
}
