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

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\UserRepository")
 * @ORM\Table(name="hlp_nebula_user")
 */
class User extends BaseUser implements OwnerInterface
{
  /**
   * @var Integer
   */
  private $nbMods = null;
  
  /**
   * @var Integer
   */
  private $nbBranches = null;
  
  /**
   * @var Integer
   */
  private $nbBuilds = null;
    
  /**
   * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\FSMod", mappedBy="userAsOwner")
   */
  private $mods;
  
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;
  
  /**
   * @var \DateTime
   *
   * @ORM\Column(name="joined", type="datetime")
   */
  private $joined;
  
  /**
   * Constructor
   */
  public function __construct()
  {
      parent::__construct();

      $this->mods = new \Doctrine\Common\Collections\ArrayCollection();
      $this->joined = new \Datetime;
  }

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
   * Add mods
   *
   * @param \HLP\NebulaBundle\Entity\FSMod $mods
   * @return User
   */
  public function addMod(\HLP\NebulaBundle\Entity\FSMod $mods)
  {
      $this->mods[] = $mods;
      $mods->setOwner($this);
      return $this;
  }

  /**
   * Remove mods
   *
   * @param \HLP\NebulaBundle\Entity\FSMod $mods
   */
  public function removeMod(\HLP\NebulaBundle\Entity\FSMod $mods)
  {
      $this->mods->removeElement($mods);
  }

  /**
   * Get mods
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getMods()
  {
      return $this->mods;
  }
  
  public function getName()
  {
      return $this->username;
  }
  
  public function getNameCanonical()
  {
      return $this->usernameCanonical;
  }
  

    /**
     * Set joined
     *
     * @param \DateTime $joined
     * @return User
     */
    public function setJoined($joined)
    {
        $this->joined = $joined;

        return $this;
    }

    /**
     * Get joined
     *
     * @return \DateTime 
     */
    public function getJoined()
    {
        return $this->joined;
    }
  
  /**
   * Return the number of tags related to the blog post.
   *
   * @return Integer

   */
  public function getNbMods()
  {
    if (is_null($this->nbMods))
    {
      $this->nbMods = $this->getMods()->count();
    }

    return $this->nbMods;
  }
  
  /**
   * Return the number of tags related to the blog post.
   *
   * @return Integer

   */
  public function getNbBranches()
  {
    if (is_null($this->nbBranches))
    {
      $this->nbBranches = 0;
      foreach($this->getMods() as $mod)
      {
        $this->nbBranches += $mod->getBranches()->count();
      }
    }

    return $this->nbBranches;
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
      $this->nbBuilds = 0;
      foreach($this->getMods() as $mod)
      {
        foreach($mod->getBranches() as $branch)
        {
          $this->nbBuilds += $branch->getBuilds()->count();
        }
      }
    }

    return $this->nbBuilds;
  }
}
