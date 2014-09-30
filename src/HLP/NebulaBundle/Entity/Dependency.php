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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dependency
 *
 * @ORM\Table(name="hlp_nebula_dependency")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\DependencyRepository")
 */
class Dependency
{
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Package", inversedBy="dependencies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $package;
    
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
     * @ORM\Column(name="depId", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[-\w]+$/", message="Special characters not allowed in the dependency mod ID.")
     * @Assert\Regex("/^-/", match=false, message="Dash not allowed at the beginning of the dependency mod ID.")
     */
    private $depId;

    /**
     * @var array
     *
     * @ORM\Column(name="packages", type="array")
     * @Assert\All({
     *     @Assert\Regex("/^[-\w]+$/", message="Special characters not allowed in the dependency package name."),
     *     @Assert\Regex("/^-/", match=false, message="Dash not allowed at the beginning of a dependency package name."),
     *     @Assert\Length(max=255)
     * })
     */
    private $depPkgs;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $version;


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
     * Set depId
     *
     * @param string $depId
     * @return Dependency
     */
    public function setDepId($depId)
    {
        $this->depId = $depId;

        return $this;
    }

    /**
     * Get depId
     *
     * @return string 
     */
    public function getDepId()
    {
        return $this->depId;
    }

    /**
     * Set packages
     *
     * @param array $packages
     * @return Dependency
     */
    public function setDepPkgs($depPkgs)
    {
        $this->depPkgs = array_values($depPkgs);

        return $this;
    }

    /**
     * Get packages
     *
     * @return array 
     */
    public function getDepPkgs()
    {
        return $this->depPkgs;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Dependency
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set package
     *
     * @param \HLP\NebulaBundle\Entity\Package $package
     * @return Dependency
     */
    public function setPackage(\HLP\NebulaBundle\Entity\Package $package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Get package
     *
     * @return \HLP\NebulaBundle\Entity\Package 
     */
    public function getPackage()
    {
        return $this->package;
    }
    
    public function __clone()
    {
         if ($this->id) {
            $this->id = null;
         }
    }
}
