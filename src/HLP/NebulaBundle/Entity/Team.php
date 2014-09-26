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

/**
 * Team
 *
 * @ORM\Table(name="hlp_nebula_team")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\TeamRepository")
 */
class Team
{
    /**
     * @ORM\OneToMany(targetEntity="HLP\NebulaBundle\Entity\FSMod", mappedBy="teamAsOwner")
     */
    private $mods;
    
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
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCanonical", type="string", length=255)
     */
    private $nameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * @return Team
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
     * Set nameCanonical
     *
     * @param string $nameCanonical
     * @return Team
     */
    public function setNameCanonical($nameCanonical)
    {
        $this->nameCanonical = $nameCanonical;

        return $this;
    }

    /**
     * Get nameCanonical
     *
     * @return string 
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Team
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
     * Set created
     *
     * @param \DateTime $created
     * @return Team
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mods = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add mods
     *
     * @param \HLP\NebulaBundle\Entity\FSMod $mods
     * @return Team
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
}
