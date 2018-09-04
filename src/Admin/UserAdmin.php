<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('phone')
            ->add('group')
            ->add('isAdmin')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('firstName', null, [
                'label' => 'Имя',
            ])
            ->add('lastName', null, [
                'label' => 'Фамилия',
            ])
            ->add('email')
            ->add('phone', null, [
                'label' => 'Телефон',
            ])
            ->add('group.name', null, [
                'label' => 'Группа',
            ])
            ->add('isAdmin')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ]
            ])
        ;
    }
}

