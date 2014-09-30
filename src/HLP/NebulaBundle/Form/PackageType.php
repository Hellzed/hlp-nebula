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

class PackageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',           'text')
            ->add('notes',          'textarea',   array('required' => false))
            ->add('status',         'choice', array(
                    'choices'   => array(
                        'required'    => 'Required',
                        'recommended' => 'Recommended',
                        'optional'    => 'Optional')
                    )
                )
            ->add('envVars',        'collection', array(
                    'type'            => new EnvVarType(),
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'by_reference'    => false,
                    'prototype'       => true,
                    'prototype_name'  => '__envVars_prototype__')
                )
            ->add('dependencies',   'collection', array(
                    'type'            => new DependencyType(),
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'by_reference'    => false,
                    'prototype'       => true,
                    'prototype_name'  => '__dependencies_prototype__')
                )
            ->add('files',          'collection', array(
                    'type'            => new FileType(),
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'by_reference'    => false,
                    'prototype'       => true,
                    'prototype_name'  => '__files_prototype__')
                )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HLP\NebulaBundle\Entity\Package'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hlp_nebulabundle_package';
    }
}
