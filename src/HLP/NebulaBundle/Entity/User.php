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

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\UserRepository")
 * @ORM\Table(name="hlp_nebula_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\ManyToMany(targetEntity="Meta", inversedBy="users")
     * @ORM\JoinTable(name="hlp_nebula_user_meta")
     **/
    private $metas;
  
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
      
      $this->metas = new \Doctrine\Common\Collections\ArrayCollection();

      $this->joined = new \Datetime;
  }
  
  public function __toString()
  {
      return $this->usernameCanonical;
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
     * @Assert\Callback
     * Need to change this to avoid team name duplicates
     */
    public function forbiddenWords(ExecutionContextInterface $context)
    {
      $forbiddenWords = Array('test');
      
      if(in_array($this->usernameCanonical, $forbiddenWords)) {
        $context->addViolationAt(
            'usernameCanonical',
            'Username is a forbidden word ("'.$this->usernameCanonical.'") !',
            array(),
            null
            );
       }
    }

    /**
     * Add metas
     *
     * @param \HLP\NebulaBundle\Entity\Meta $metas
     * @return User
     */
    public function addMeta(\HLP\NebulaBundle\Entity\Meta $metas)
    {
        $this->metas[] = $metas;

        return $this;
    }

    /**
     * Remove metas
     *
     * @param \HLP\NebulaBundle\Entity\Meta $metas
     */
    public function removeMeta(\HLP\NebulaBundle\Entity\Meta $metas)
    {
        $this->metas->removeElement($metas);
    }

    /**
     * Get metas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMetas()
    {
        return $this->metas;
    }
}
