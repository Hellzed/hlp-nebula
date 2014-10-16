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
 * Action
 *
 * @ORM\Table(name="hlp_nebula_action")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\ActionRepository")
 */
class Action
{
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Build", inversedBy="actions")
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(name="paths", type="array")
     * @Assert\Count(
     *      min = "1",
     *      minMessage = "You must specify at least one path for the action."
     * )
     * @Assert\All({
     *     @Assert\Length(max=255),
     *     @Assert\Regex(
     *     pattern="/^([\\\/]?[^\0\\\/:\?\x22<>\|]+)*[\\\/]?$/",
     *     message="The action path must be a valid relative path."
     *     )
     * })
     */
    private $paths;

    /**
     * @var boolean
     *
     * @ORM\Column(name="glob", type="boolean")
     */
    private $glob;

    /**
     * @var string
     *
     * @ORM\Column(name="dest", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex(
     *     pattern="/^([\\\/]?[^\0\\\/:\*\?\x22<>\|]+)*[\\\/]?$/",
     *     message="The action destination must be a valid relative path."
     * )
     */
    private $dest;


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
     * @return Action
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
     * Set paths
     *
     * @param array $paths
     * @return Action
     */
    public function setPaths($paths)
    {
        $this->paths = array_values($paths);
        
        foreach($this->paths as $key => $path)
        {
          $this->paths[$key] = trim(str_replace('\\', '/', $path), '/');
        }

        return $this;
    }

    /**
     * Get paths
     *
     * @return array 
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Set glob
     *
     * @param boolean $glob
     * @return Action
     */
    public function setGlob($glob)
    {
        $this->glob = $glob;

        return $this;
    }

    /**
     * Get glob
     *
     * @return boolean 
     */
    public function getGlob()
    {
        return $this->glob;
    }

    /**
     * Set dest
     *
     * @param string $dest
     * @return Action
     */
    public function setDest($dest)
    {
        $this->dest = trim(str_replace('\\', '/', $dest), '/');
        if($this->type == 'delete') {
            $this->dest = null;
        }
        
        if(($this->dest == null) && ($this->type == 'move'))
        {
          $this->dest = '';
        }
        return $this;
    }

    /**
     * Get dest
     *
     * @return string 
     */
    public function getDest()
    {
        return $this->dest;
    }

    /**
     * Set build
     *
     * @param \HLP\NebulaBundle\Entity\Build $build
     * @return Action
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
    
    public function __clone()
    {
         if ($this->id) {
            $this->id = null;
         }
    }
}
