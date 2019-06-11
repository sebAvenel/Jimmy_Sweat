<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'Nom'], 'label' => false])
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'Email'], 'label' => false])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => ['attr' => ['placeholder' => 'mot de passe'], 'label' => false],
                'second_options' => ['attr' => ['placeholder' => 'Confirmation mot de passe'], 'label' => false],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
