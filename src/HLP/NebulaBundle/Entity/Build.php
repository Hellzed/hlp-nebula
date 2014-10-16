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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Build
 *
 * @ORM\Table(name="hlp_nebula_build")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\BuildRepository")
 * @UniqueEntity(fields={"branch", "versionMajor", "versionMinor", "versionPatch", "versionPreRelease", "versionMetadata"},  ignoreNull=false, message="Same version error (across a mod branch).")
 */
class Build
{
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Package", mappedBy="build", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $packages;
    
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Action", mappedBy="build", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $actions;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Branch", inversedBy="builds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $branch;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="versionMajor", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 999,
     *      minMessage = "Major version number must be positive",
     *      maxMessage = "A major version number over 999, really ?"
     * )
     */
    private $versionMajor;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionMinor", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 999,
     *      minMessage = "Minor version number must be positive",
     *      maxMessage = "A minor version number over 999, really ?"
     * )
     */
    private $versionMinor;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionPatch", type="integer")
     * @Assert\Range(
     *      min = 0,
     *      max = 99999,
     *      minMessage = "Patch version number must be positive",
     *      maxMessage = "A patch version number over 99,999, really ?"
     * )
     */
    private $versionPatch;

    /**
     * @var string
     *
     * @ORM\Column(name="versionPreRelease", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[0-9A-Za-z-]+(\.[0-9A-Za-z-]+)*$/", message="Special characters not allowed in the pre-release field.")
     */
    private $versionPreRelease;

    /**
     * @var string
     *
     * @ORM\Column(name="versionMetadata", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[0-9A-Za-z-]+(\.[0-9A-Za-z-]+)*$/", message="Special characters not allowed in the metadata.")
     */
    private $versionMetadata;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     * @Assert\DateTime()
     */
    private $updated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isReady", type="boolean")
     */
    private $isReady;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isFailed", type="boolean")
     */
    private $isFailed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="generated_JSON", type="text", nullable=true)
     */
    private $generatedJSON;
    
    /**
     * @var string
     *
     * @ORM\Column(name="converterToken", type="string", length=255, nullable=true)
     */
    private $converterToken;
    
    /**
     * @var string
     *
     * @ORM\Column(name="converterTicket", type="string", length=255, nullable=true)
     */
    private $converterTicket;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;
    
    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex(
     *     pattern="/^([\\\/]?[^\0\\\/:\*\?\x22<>\|]+)*[\\\/]?$/",
     *     message="The mod folder must be a valid relative path."
     * )
     */
    private $folder;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getVersion()
    {
        $this->version = $this->versionMajor.'.'.$this->versionMinor.'.'.$this->versionPatch;
        
        if(isset($this->versionPreRelease))
        {
            $this->version .= '-'.$this->versionPreRelease;
        }
        
        if(isset($this->versionMetadata))
        {
            $this->version .= '+'.$this->versionMetadata;
        }
        
        return $this->version;
    }
    
    /**
     * Set versionMajor
     *
     * @param integer $versionMajor
     * @return Build
     */
    public function setVersionMajor($versionMajor)
    {
        $this->versionMajor = $versionMajor;

        return $this;
    }

    /**
     * Get versionMajor
     *
     * @return integer 
     */
    public function getVersionMajor()
    {
        return $this->versionMajor;
    }

    /**
     * Set versionMinor
     *
     * @param integer $versionMinor
     * @return Build
     */
    public function setVersionMinor($versionMinor)
    {
        $this->versionMinor = $versionMinor;

        return $this;
    }

    /**
     * Get versionMinor
     *
     * @return integer 
     */
    public function getVersionMinor()
    {
        return $this->versionMinor;
    }

    /**
     * Set versionPatch
     *
     * @param integer $versionPatch
     * @return Build
     */
    public function setVersionPatch($versionPatch)
    {
        $this->versionPatch = $versionPatch;

        return $this;
    }

    /**
     * Get versionPatch
     *
     * @return integer 
     */
    public function getVersionPatch()
    {
        return $this->versionPatch;
    }

    /**
     * Set versionPreRelease
     *
     * @param string $versionPreRelease
     * @return Build
     */
    public function setVersionPreRelease($versionPreRelease)
    {
        $this->versionPreRelease = $versionPreRelease;

        return $this;
    }

    /**
     * Get versionPreRelease
     *
     * @return string 
     */
    public function getVersionPreRelease()
    {
        return $this->versionPreRelease;
    }

    /**
     * Set versionMetadata
     *
     * @param string $versionMetadata
     * @return Build
     */
    public function setVersionMetadata($versionMetadata)
    {
        $this->versionMetadata = $versionMetadata;

        return $this;
    }

    /**
     * Get versionMetadata
     *
     * @return string 
     */
    public function getVersionMetadata()
    {
        return $this->versionMetadata;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Build
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set isReady
     *
     * @param boolean $isReady
     * @return Build
     */
    public function setIsReady($isReady)
    {
        $this->isReady = $isReady;

        return $this;
    }

    /**
     * Get isReady
     *
     * @return boolean 
     */
    public function getIsReady()
    {
        return $this->isReady;
    }

    /**
     * Set isFailed
     *
     * @param boolean $isFailed
     * @return Build
     */
    public function setIsFailed($isFailed)
    {
        $this->isFailed = $isFailed;

        return $this;
    }

    /**
     * Get isFailed
     *
     * @return boolean 
     */
    public function getIsFailed()
    {
        return $this->isFailed;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Build
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
     * Set branch
     *
     * @param \HLP\NebulaBundle\Entity\Branch $branch
     * @return Build
     */
    public function setBranch(\HLP\NebulaBundle\Entity\Branch $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \HLP\NebulaBundle\Entity\Branch 
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set branchId
     *
     * @param string $branchId
     * @return Build
     */
    public function setBranchId($branchId)
    {
        $this->branchId = $branchId;

        return $this;
    }

    /**
     * Get branchId
     *
     * @return string 
     */
    public function getBranchId()
    {
        return $this->branchId;
    }

    /**
     * Set folder
     *
     * @param string $folder
     * @return Build
     */
    public function setFolder($folder)
    {
        $this->folder = trim(str_replace('\\', '/', $folder), '/');

        return $this;
    }

    /**
     * Get folder
     *
     * @return string 
     */
    public function getFolder()
    {
        return $this->folder;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isReady = false;
        $this->isFailed = false;
        $this->updated = new \Datetime;
        
        $this->packages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
      return $this->getVersion();
    }
    
    public function __clone()
    {
         if ($this->id) {
            $this->id = null;
            $this->versionPatch++;
            $this->versionPreRelease = null;
            $this->versionMetadata = null;
            $this->isReady = false;
            $this->isFailed = false;
            $this->generatedJSON = null;
            $this->converterToken = null;
            $this->converterTicket = null;
            $this->updated = new \Datetime;
            
            $newPackages = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($this->packages as $package) {
              $newPackage = clone $package;
              $newPackages[] = $newPackage;
              $newPackage->setBuild($this);
            }
            $this->packages = $newPackages;
            
            $newActions = new \Doctrine\Common\Collections\ArrayCollection();
            foreach($this->actions as $action) {
              $newAction = clone $action;
              $newActions[] = $newAction;
              $newAction->setBuild($this);
            }
            $this->actions = $newActions;
         }
    }

    /**
     * Add packages
     *
     * @param \HLP\NebulaBundle\Entity\Package $packages
     * @return Build
     */
    public function addPackage(\HLP\NebulaBundle\Entity\Package $packages)
    {
        $this->packages[] = $packages;
        $packages->setBuild($this);
        return $this;
    }

    /**
     * Remove packages
     *
     * @param \HLP\NebulaBundle\Entity\Package $packages
     */
    public function removePackage(\HLP\NebulaBundle\Entity\Package $packages)
    {
        $this->packages->removeElement($packages);
    }

    /**
     * Get packages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Add actions
     *
     * @param \HLP\NebulaBundle\Entity\Action $actions
     * @return Build
     */
    public function addAction(\HLP\NebulaBundle\Entity\Action $actions)
    {
        $this->actions[] = $actions;
        $actions->setBuild($this);
        return $this;
    }

    /**
     * Remove actions
     *
     * @param \HLP\NebulaBundle\Entity\Action $actions
     */
    public function removeAction(\HLP\NebulaBundle\Entity\Action $actions)
    {
        $this->actions->removeElement($actions);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    /**
     * @Assert\Callback
     */
    public function packageNamesUnique(ExecutionContextInterface $context)
    {
      $packageNames = Array();
      
      foreach ($this->packages as $key => $package) {
        $packageNames[$key] = $package->getName();
      }
      
      if(count($packageNames) !== count(array_unique($packageNames))) {
        $context->addViolationAt(
            'packages',
            'Duplicated package names !',
            array(),
            null
        );
      }
    }

    /**
     * Set generatedJSON
     *
     * @param string $generatedJSON
     * @return Build
     */
    public function setGeneratedJSON($generatedJSON)
    {
        $this->generatedJSON = $generatedJSON;

        return $this;
    }

    /**
     * Get generatedJSON
     *
     * @return string 
     */
    public function getGeneratedJSON()
    {
        return $this->generatedJSON;
    }

    /**
     * Set converterToken
     *
     * @param string $converterToken
     * @return Build
     */
    public function setConverterToken($converterToken)
    {
        $this->converterToken = $converterToken;

        return $this;
    }

    /**
     * Get converterToken
     *
     * @return string 
     */
    public function getConverterToken()
    {
        return $this->converterToken;
    }

    /**
     * Set converterTicket
     *
     * @param string $converterTicket
     * @return Build
     */
    public function setConverterTicket($converterTicket)
    {
        $this->converterTicket = $converterTicket;

        return $this;
    }

    /**
     * Get converterTicket
     *
     * @return string 
     */
    public function getConverterTicket()
    {
        return $this->converterTicket;
    }
}
