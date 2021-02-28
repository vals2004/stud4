<?php
namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use App\Entity\Lesson;

class LessonAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('subject', null, [
                'required' => true,
            ])
            ->add('type')
            ->add('documentFile', VichFileType::class)
            //->add('documentLabFile', VichFileType::class)
            //->add('documentLabFile1', VichFileType::class)
            //->add('documentLabFile2', VichFileType::class)
            //->add('documentLabFile3', VichFileType::class)
            ->add('date', DatePickerType::class)
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
            ->addIdentifier('subject')
            ->add('type')
            ->add('document')
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

