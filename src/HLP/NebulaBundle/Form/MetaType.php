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

class MetaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('metaId',          'text')
            ->add('notes',          'textarea',   array('required' => false))
            ->add('firstRelease',   'date',       array('years'  => range(1999, date('Y')),
                                                        'format' => 'dd-MM-yyyy'))
            ->add('title',          'text')
            ->add('description',    'textarea',   array('required' => false))
            ->add('features',       'collection', array('type'        => 'text',
                                                    'allow_add'       => true,
                                                    'allow_delete'    => true,
                                                    'prototype'       => true,
                                                    'prototype_name'  => '__features_prototype__'))
            ->add('authors',        'collection', array(
                    'type'            => new AuthorType(),
                    'error_bubbling'  => false,
                    'allow_add'       => true,
                    'allow_delete'    => true,
                    'prototype'       => true,
                    'by_reference'    => false,
                    'prototype_name'  => '__authors_prototype__'))
            ->add('keywords',       'text',       array('required' => false))
            ->add('categories', 'entity', array(
                    'class'    => 'HLPNebulaBundle:Category',
                    'expanded'   => true,
                    'multiple' => true))
            ->add('logo',           new LogoType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HLP\NebulaBundle\Entity\Meta'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hlp_nebulabundle_meta';
    }
}
