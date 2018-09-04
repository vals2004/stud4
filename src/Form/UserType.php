<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Group;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => 'Имя',
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => 'Фамилия',
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'required' => true,
                'label' => 'Группа',
            ])
            ->add('phone', TextType::class, [
                'required' => true,
                'label' => 'Телефон',
            ])
            ->add('email', EmailType::class, [
                'required' => true, 
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Пароль',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
            ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }


}
