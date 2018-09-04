<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ExamAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('user.group')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user')
            ->add('dateStart')
            ->add('dateEnd')
            ->add('updatedAt', null, [
                'label' => 'Last activity',
            ])
            ->add('questions', null, [
                'template' => 'CRUD/list_count.html.twig',
            ])
            ->add('answers', null, [
                'template' => 'CRUD/list_count.html.twig',
            ])
            ->add('result', null, [
                'template' => 'CRUD/list_summary.html.twig',
            ])
        ;
    }
}

