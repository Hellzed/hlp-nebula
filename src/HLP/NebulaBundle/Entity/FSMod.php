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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FSMod
 *
 * @ORM\Table(name="hlp_nebula_fsmod")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\FSModRepository")
 * @UniqueEntity(fields={"userAsOwner","teamAsOwner","modId"}, ignoreNull=false, message="Same modId error.")
 */
class FSMod
{
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Branch", mappedBy="mod", cascade={"remove"})
     */
    private $branches;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\User", inversedBy="mods")
     */
    private $userAsOwner;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Team", inversedBy="mods")
     */
    private $teamAsOwner;
    
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
     * @ORM\Column(name="modId", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[-\w]+$/", message="Special characters not allowed in the mod ID.")
     * @Assert\Regex("/^-/", match=false, message="Dash not allowed at the beginning of the mod ID.")
     */
    private $modId;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="firstRelease", type="date")
     * @Assert\Date()
     */
    private $firstRelease;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(name="features", type="array")
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Length(max=255)
     * })
     */
    private $features;


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
     * Set modId
     *
     * @param string $modId
     * @return FSMod
     */
    public function setModId($modId)
    {
        $this->modId = $modId;

        return $this;
    }

    /**
     * Get modId
     *
     * @return string 
     */
    public function getModId()
    {
        return $this->modId;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return FSMod
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
     * Set firstRelease
     *
     * @param \DateTime $firstRelease
     * @return FSMod
     */
    public function setFirstRelease($firstRelease)
    {
        $this->firstRelease = $firstRelease;

        return $this;
    }

    /**
     * Get firstRelease
     *
     * @return \DateTime 
     */
    public function getFirstRelease()
    {
        return $this->firstRelease;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return FSMod
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return FSMod
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set features
     *
     * @param array $features
     * @return FSMod
     */
    public function setFeatures($features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Get features
     *
     * @return array 
     */
    public function getFeatures()
    {
        return $this->features;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->branches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add branches
     *
     * @param \HLP\NebulaBundle\Entity\Branch $branches
     * @return FSMod
     */
    public function addBranch(\HLP\NebulaBundle\Entity\Branch $branches)
    {
        $this->branches[] = $branches;
        $branches->setMod($this);
        return $this;
    }

    /**
     * Remove branches
     *
     * @param \HLP\NebulaBundle\Entity\Branch $branches
     */
    public function removeBranch(\HLP\NebulaBundle\Entity\Branch $branches)
    {
        $this->branches->removeElement($branches);
    }

    /**
     * Get branches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBranches()
    {
        return $this->branches;
    }


    /**
     * Set userAsOwner
     *
     * @param \HLP\NebulaBundle\Entity\User $userAsOwner
     * @return FSMod
     */
    public function setUserAsOwner(\HLP\NebulaBundle\Entity\User $userAsOwner = null)
    {
        $this->userAsOwner = $userAsOwner;

        return $this;
    }

    /**
     * Get userAsOwner
     *
     * @return \HLP\NebulaBundle\Entity\User 
     */
    public function getUserAsOwner()
    {
        return $this->userAsOwner;
    }

    /**
     * Set teamAsOwner
     *
     * @param \HLP\NebulaBundle\Entity\Team $teamAsOwner
     * @return FSMod
     */
    public function setTeamAsOwner(\HLP\NebulaBundle\Entity\Team $teamAsOwner = null)
    {
        $this->teamAsOwner = $teamAsOwner;

        return $this;
    }

    /**
     * Get teamAsOwner
     *
     * @return \HLP\NebulaBundle\Entity\Team 
     */
    public function getTeamAsOwner()
    {
        return $this->teamAsOwner;
    }
    
    public function setOwner(\HLP\NebulaBundle\Entity\OwnerInterface $owner)
    {
        
        $ownerClass = get_class($owner);
        
        if($ownerClass == 'HLP\NebulaBundle\Entity\User')
        {
          $this->setUserAsOwner($owner);
        }
        
        if($ownerClass == 'HLP\NebulaBundle\Entity\Team')
        {
          $this->setTeamAsOwner($owner);
        }
        
        return $this;
    }
    
    public function getOwner()
    {
        if(isset($this->teamAsOwner))
        {   
          return $this->teamAsOwner;
        }
        
        if(isset($this->userAsOwner))
        {   
          return $this->userAsOwner;
        }
    }
    
    
}
