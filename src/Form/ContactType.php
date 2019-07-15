<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['attr' => ['placeholder' => 'Nom'], 'label' => false])
            ->add('lastname', TextType::class, ['attr' => ['placeholder' => 'Prenom'], 'label' => false])
            ->add('phone', TextType::class, ['attr' => ['placeholder' => 'Téléphone'], 'label' => false])
            ->add('email', TextType::class, ['attr' => ['placeholder' => 'Email'], 'label' => false])
            ->add('message', TextareaType::class, ['attr' => ['placeholder' => 'Votre message', 'rows' => 3], 'label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class
        ]);
    }
}
