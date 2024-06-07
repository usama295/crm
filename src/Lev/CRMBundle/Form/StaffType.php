<?php

namespace App\Lev\CRMBundle\Form;

use App\Lev\CRMBundle\Entity\Staff;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class StaffType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('firstname', null, array('label' => 'Name'))
            ->add('email', EmailType::class, array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('superadmin', ChoiceType::class, array(
                'choices' => array('No' => '0', 'Yes' => '1'),
                'attr'     => array(
                    'class' => 'chosen-select',
                )
            ))->add('enabled', ChoiceType::class, array(
                'choices' => array('No' => '0', 'Yes' => '1'),
                'attr'     => array(
                    'class' => 'chosen-select',
                )
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' =>  PasswordType::class,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password', 'attr' => array('size' => '2') ),
                'second_options' => array('label' => 'form.password_confirmation', 'attr' => array('size' => '2')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('staffroles', EntityType::class, array(
                'class'    => \App\Lev\CRMBundle\Entity\StaffRole::class,
                 'choice_label' => 'name',
                'placeholder' => 'Select one or more staff roles',
                'multiple' => true,
                'attr'     => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Select one or more staff roles'
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
            'data_class' => '\App\Lev\CRMBundle\Entity\Staff'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lev_crmbundle_staff';
    }
}
