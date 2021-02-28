<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Group;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class MassMailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class, [
                'required' => true,
            ])
            ->add('message', CKEditorType::class, [
                'required' => true,
                'label' => '{name}',
                'attr' => [
		    'rows' => 20,
		    'cols' => 100,
                ],
            ])
            ->add('recipients', TextareaType::class, [
                'required' => true,
                'label' => 'email___name___file',
                'attr' => [
                    'rows' => 10,
                ],
            ])
            ->add('attachments', FileType::class, [
                'required' => false,
                'label' => 'zip with *.pdf',
            ])
            ->add('submit', SubmitType::class, [
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'form';
    }


}
