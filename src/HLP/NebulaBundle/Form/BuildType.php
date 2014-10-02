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

namespace HLP\NebulaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BuildType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('versionMajor',       'integer')
            ->add('versionMinor',       'integer')
            ->add('versionPatch',       'integer')
            ->add('versionPreRelease',  'text',     array('required' => false))
            ->add('versionMetadata',    'text',     array('required' => false))
            ->add('notes',              'textarea', array('required' => false))
            ->add('folder',             'text',     array('required' => false))
            ->add('packages',           'collection', array(
                    'type'            => new PackageType(),
                    'error_bubbling'  => false,
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'prototype'       => true,
                    'by_reference'    => false,
                    'prototype_name'  => '__packages_prototype__')
                )
            ->add('actions',            'collection', array(
                    'type'            => new ActionType(),
                    'error_bubbling'  => false,
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'prototype'       => true,
                    'by_reference'    => false,
                    'prototype_name'  => '__actions_prototype__')
                )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HLP\NebulaBundle\Entity\Build'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hlp_nebulabundle_build';
    }
}
