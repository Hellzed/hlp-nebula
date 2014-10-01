<?php
namespace HLP\NebulaBundle\Entity;

trait OwnerCounters {
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
  
  // Double check the final class can run getMods()
  abstract public function getMods();
  
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
