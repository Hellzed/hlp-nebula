<?php
namespace HLP\NebulaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BranchEditType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->remove('branchId');
  }

  public function getName()
  {
    return 'hlp_nebulabundle_branch_edit';
  }

  public function getParent()
  {
    return new BranchType();
  }
}
