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
     * @ORM\ManyToOne(targetEntity="HLP\NebulaBundle\Entity\Meta", inversedBy="authors")
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
     * Set meta
     *
     * @param \HLP\NebulaBundle\Entity\Meta $meta
     * @return Author
     */
    public function setMeta(\HLP\NebulaBundle\Entity\Meta $meta = null)
    {
        $this->mod = $meta;

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
}
