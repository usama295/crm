<?php

namespace App\Lev\CRMBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Yaml\Yaml;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class StaffRoleType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $rolesYaml = Yaml::parse(file_get_contents(__DIR__ . '/../../../../config/api_roles.yaml'));
        $rolesChoices = array(
            'ROLE_ADMIN' => 'Administrator'
        );

        foreach($rolesYaml['parameters']['lev_api.other_roles'] as $role => $label) {
            $rolesChoices[$role] = $label;
        }

        foreach($rolesYaml['parameters']['lev_api.crud_roles'] as $entity => $roles) {
            $roleId    = strtoupper('ROLE_' . $entity);
            $roleLabel = ucfirst($entity) . ' FULL ACCESS';
            $rolesChoices[strtoupper($entity)][$roleId] = $roleLabel;
            foreach($roles as $role) {
                $roleId    = strtoupper('ROLE_' . $entity . '_' . $role);
                $roleLabel = ucfirst($entity) . ' ' . strtoupper($role);
                $rolesChoices[strtoupper($entity)][$roleId] = $roleLabel;
            }
        }

        $builder
            ->add('name')
            ->add('superadmin', ChoiceType::class,
             array(
                'choices' => array('No' => '0', 'Yes' => '1'),
                'attr'     => array(
                    'class' => 'chosen-select',
                )
            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => $rolesChoices,
                'multiple' => true,
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-placeholder' => 'Select one or more roles'
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
            'data_class' => 'App\Lev\CRMBundle\Entity\StaffRole'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lev_crmbundle_staffrole';
    }
}
