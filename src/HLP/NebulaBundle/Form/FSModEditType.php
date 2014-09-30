<?php
namespace HLP\NebulaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FSModEditType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->remove('modId');
  }

  public function getName()
  {
    return 'hlp_nebulabundle_fsmod_edit';
  }

  public function getParent()
  {
    return new FSModType();
  }
}
