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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Branch
 *
 * @ORM\Table(name="hlp_nebula_branch")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\BranchRepository")
 * @UniqueEntity(fields={"mod","branchId"}, message="A branch with the same ID already exists in the repository.")
 */
class Branch
{
    /**
     * @var Integer
     */
    private $nbBuilds = null;
    
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\Build", mappedBy="branch", cascade={"remove"})
     */
    private $builds;
    
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\FSMod", inversedBy="branches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mod;
  
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
     * @var boolean
     *
     * @ORM\Column(name="isDefault", type="boolean")
     */
    private $isDefault;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;


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
     * Set mod
     *
     * @param \HLP\NebulaBundle\Entity\FSMod $mod
     * @return Branch
     */
    public function setMod(\HLP\NebulaBundle\Entity\FSMod $mod)
    {
        $this->mod = $mod;

        return $this;
    }

    /**
     * Get mod
     *
     * @return \HLP\NebulaBundle\Entity\FSMod 
     */
    public function getMod()
    {
        return $this->mod;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
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
        $builds->setBranch($this);
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
   * Return the number of tags related to the blog post.
   *
   * @return Integer

   */
  public function getNbBuilds()
  {
    if (is_null($this->nbBuilds))
    {
      $this->nbBuilds = $this->getBuilds()->count();
    }

    return $this->nbBuilds;
  }
  
  /**
   * @Assert\Callback
   */
  public function forbiddenWords(ExecutionContextInterface $context)
  {
    $forbiddenWords = Array('branches','details','activity');
    
    if(in_array($this->branchId, $forbiddenWords)) {
      $context->addViolationAt(
          'branchId',
          'Branch ID is a forbidden word ("'.$this->branchId.'") !',
          array(),
          null
          );
     }
  }
}
