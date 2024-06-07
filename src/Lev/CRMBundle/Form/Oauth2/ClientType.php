<?php

namespace App\Lev\CRMBundle\Form\Oauth2;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use OAuth2\OAuth2;

class ClientType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $grantTypes = array(
            'Authorization Code' => OAuth2::GRANT_TYPE_AUTH_CODE,
             'Token' => OAuth2::GRANT_TYPE_IMPLICIT,
             'Password' => OAuth2::GRANT_TYPE_USER_CREDENTIALS,
             'Client Credentials' => OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
             'Refresh Token' => OAuth2::GRANT_TYPE_REFRESH_TOKEN,
//            OAuth2::GRANT_TYPE_EXTENSIONS         => 'Extensions',
        );

        $builder
            ->add('redirectUris', CollectionType::class, array(
                'allow_add'    => true,
                'prototype'    => true,
                'allow_delete' => true,
//                'options'      => array(
//                    'required'  => true,
//                )
            ))
            ->add('allowedGrantTypes', ChoiceType::class, array(
                'choices'  => $grantTypes,
                'multiple' => true,
                'attr'     => array(
                    'class'            => 'chosen-select',
                    'data-placeholder' => 'Select one or more grant types'
                )
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Lev\CRMBundle\Entity\Oauth2\Client'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lev_crmbundle_oauth2_client';
    }
}
