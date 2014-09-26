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
 * EnvVar
 *
 * @ORM\Table(name="hlp_nebula_environment_variable")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\EnvVarRepository")
 */
class EnvVar
{
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Package", inversedBy="envVars")
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


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
     * Set type
     *
     * @param string $type
     * @return EnvVar
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return EnvVar
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set package
     *
     * @param \HLP\NebulaBundle\Entity\Package $package
     * @return EnvVar
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
}
