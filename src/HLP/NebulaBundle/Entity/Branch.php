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
 * Branch
 *
 * @ORM\Table(name="hlp_nebula_branch")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\BranchRepository")
 * @UniqueEntity(fields={"meta","branchId"}, message="A branch with the same ID already exists in the repository.")
 */
class Branch
{
    /**
     * @var Integer
     *
     * @ORM\Column(name="nbBuilds", type="integer")
     */
    private $nbBuilds;
    
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Build", mappedBy="branch", cascade={"remove"})
     */
    private $builds;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Meta", inversedBy="branches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meta;
  
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
     * @ORM\Column(name="branchId", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[-\w]+$/", message="Special characters not allowed in the branch ID.")
     * @Assert\Regex("/^-/", match=false, message="Dash not allowed at the beginning of the branch ID.")
     */
    private $branchId;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;


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
     * Set branchId
     *
     * @param string $branchId
     * @return Branch
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
     * Set notes
     *
     * @param string $notes
     * @return Branch
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
     * Set name
     *
     * @param string $name
     * @return Branch
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
     * Set meta
     *
     * @param \HLP\NebulaBundle\Entity\Meta $meta
     * @return Branch
     */
    public function setMeta(\HLP\NebulaBundle\Entity\Meta $meta)
    {
        $this->meta = $meta;
        
        return $this;
    }

    /**
     * Get meta
     *
     * @return \HLP\NebulaBundle\Entity\Meta 
     */
    public function getMeta()
    {
        return $this->meta;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isDefault = false;
        $this->nbBuilds = 0;
    }
    
    public function __toString()
    {
      return $this->branchId;
    }

    /**
     * Add builds
     *
     * @param \HLP\NebulaBundle\Entity\Build $builds
     * @return Branch
     */
    public function addBuild(\HLP\NebulaBundle\Entity\Build $builds)
    {
        $this->builds[] = $builds;
        $this->nbBuilds++;
        $builds->setBranch($this);
        $this->meta->addBuild($builds);
        return $this;
    }

    /**
     * Remove builds
     *
     * @param \HLP\NebulaBundle\Entity\Build $builds
     */
    public function removeBuild(\HLP\NebulaBundle\Entity\Build $builds)
    {
        $this->builds->removeElement($builds);
        $this->nbBuilds--;
    }

    /**
     * Get builds
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBuilds()
    {
        return $this->builds;
    }
  
  /**
   * @Assert\Callback
   */
  public function forbiddenWords(ExecutionContextInterface $context)
  {
    $forbiddenWords = Array('branches','details','activity','default');
    
    if(in_array($this->branchId, $forbiddenWords)) {
      $context->addViolationAt(
          'branchId',
          'Branch ID is a forbidden word ("'.$this->branchId.'") !',
          array(),
          null
          );
     }
  }

    /**
     * Set isDefault
     *
     * @param boolean $isDefault
     * @return Branch
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean 
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set nbBuilds
     *
     * @param integer $nbBuilds
     * @return Branch
     */
    public function setNbBuilds($nbBuilds)
    {
        $this->nbBuilds = $nbBuilds;

        return $this;
    }

    /**
     * Get nbBuilds
     *
     * @return integer 
     */
    public function getNbBuilds()
    {
        return $this->nbBuilds;
    }
}
