<?php

namespace HLP\NebulaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Author
 *
 * @ORM\Table(name="hlp_nebula_author")
 * @ORM\Entity(repositoryClass="HLP\NebulaBundle\Entity\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\FSMod", inversedBy="authors")
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;


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
     * @return Author
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
     * Set website
     *
     * @param string $website
     * @return Author
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set mod
     *
     * @param \HLP\NebulaBundle\Entity\FSMod $mod
     * @return Author
     */
    public function setMod(\HLP\NebulaBundle\Entity\FSMod $mod = null)
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
}
