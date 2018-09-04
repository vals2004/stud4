<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use App\Entity\Lesson;

class LessonAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('date')
            ->add('startTime')
            ->add('endTime')
            ->add('groups')
            ->add('room')
            ->add('users')
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
            ->addIdentifier('name')
            ->add('date')
            ->add('startTime')
            ->add('endTime')
            ->add('room')
            ->add('users', null, [
                'label' => 'Студенты',
                'template' => 'CRUD/list_count.html.twig',
            ])
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                ]
            ])
        ;
    }
}

