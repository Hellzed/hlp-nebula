<?php
namespace HLP\NebulaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FSModEditType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $id = $builder->getData()->getId();
    $builder->remove('modId')
            ->add('defaultBranch', 'entity', array(
              'class'    => 'HLPNebulaBundle:Branch',
              'expanded'   => false,
              'multiple' => false,
              'mapped' => false,
              'query_builder' => function(\HLP\NebulaBundle\Entity\BranchRepository $repo) use($id) {
                return $repo->getBranchFromMod($id);
              }))
              ->remove('logo')
              ->add('logo', new LogoType(), array('required' => false))
    ;
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
